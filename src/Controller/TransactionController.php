<?php

namespace App\Controller;

use App\Entity\Transaction;
use App\Entity\VatRate;
use App\Form\TransactionType;
use App\Repository\TransactionRepository;
use App\Repository\VatRateRepository;
use App\Service\VatCalculatorService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Psr\Log\LoggerInterface;

#[Route('/transaction')]
class TransactionController extends AbstractController
{
    #[Route('/', name: 'app_transaction_index', methods: ['GET'])]
    public function index(TransactionRepository $transactionRepository, EntityManagerInterface $entityManager): Response
    {

        $vatRateRepository = $entityManager->getRepository(VatRate::class);
        $vatRate = $vatRateRepository->findLatest();
        if(!$vatRate) {
            $this->addFlash('error', 'Please add a VAT rate first');
            return $this->redirectToRoute('app_vat_rate_new');
        }
        
        return $this->render('transaction/index.html.twig', [
            'transactions' => $transactionRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_transaction_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, LoggerInterface $logger): Response
    {
        $transaction = new Transaction();
        $form = $this->createForm(TransactionType::class, $transaction);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            $vatRateRepository = $entityManager->getRepository(VatRate::class);
            $vatRate = $vatRateRepository->findLatest();
            $latestVatRate = $vatRate->getRate();
  
            $VatCalculatorService = new VatCalculatorService();

            $transaction->setCreatedAt(new \DateTime());

            $inputAmount = $transaction->getAmount();
            // calculate vat for incluse and exclusive amounts
            $vatAmountExVat = $VatCalculatorService->calcVatAmountExVat($inputAmount,$latestVatRate);
            $vatAmountIncVat = $VatCalculatorService->calcVatAmountIncVat($inputAmount,$latestVatRate);

            //calculate amount after VAT is removed
            $transactionAmountIncVat = $VatCalculatorService->calcAmountIncVat($inputAmount,$vatAmountIncVat);

            //caculate amount after VAT is added
            $transactionAmountExVat = $VatCalculatorService->calcAmountExVat($inputAmount,$vatAmountExVat);
          
            $transaction->setAmountExVat($transactionAmountExVat);
            $transaction->setAmountIncVat($transactionAmountIncVat);
            $transaction->setVatAmountExVat($vatAmountExVat);
            $transaction->setVatAmountIncVat($vatAmountIncVat);
            $transaction->setVatRate($vatRate);

            $entityManager->persist($transaction);
            $entityManager->flush();
                        
            return $this->redirectToRoute('app_transaction_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('transaction/new.html.twig', [
            'transaction' => $transaction,
            'form' => $form,
        ]);
    }
/*
    #[Route('/{id}', name: 'app_transaction_show', methods: ['GET'])]
    public function show(Transaction $transaction): Response
    {
        return $this->render('transaction/show.html.twig', [
            'transaction' => $transaction,
        ]);
    }
*/
    #[Route('/{id}/edit', name: 'app_transaction_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Transaction $transaction, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(TransactionType::class, $transaction);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_transaction_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('transaction/edit.html.twig', [
            'transaction' => $transaction,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_transaction_delete', methods: ['POST'])]
    public function delete(Request $request, Transaction $transaction, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$transaction->getId(), $request->request->get('_token'))) {
            $entityManager->remove($transaction);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_transaction_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/export', name: 'app_transaction_export', methods: ['GET', 'POST'])]
    public function export(TransactionRepository $transactionRepository): Response
    {
        $response = new StreamedResponse();
        $response->setCallback(function() use ($transactionRepository)   {
            $handle = fopen('php://output', 'w+');
    
            fputcsv($handle, ['ID', 'Input', 'VAT Rate', 'VAT Included', 'Net', 'VAT Excluded', 'Gross', 'Created Date' ], ',');

            $transactions = $transactionRepository->findAll();
            foreach ($transactions as $transaction) {

                $vatRate = $transaction->getVatRate();
                $rate = $vatRate ? $vatRate->getRate() : 'N/A';

                fputcsv($handle, [
                    $transaction->getId(),
                    $transaction->getAmount(),
                    $rate,
                    $transaction->getVatAmountIncVat(),
                    $transaction->getAmountIncVat(),
                    $transaction->getVatAmountExVat(),
                    $transaction->getAmountExVat(),
                    $transaction->getCreatedAt()->format('Y-m-d H:i:s')
                ], ',');
            }
            fclose($handle);
        });

        $response->setStatusCode(200);
        $response->headers->set('Content-Type', 'text/csv; charset=utf-8');
        $response->headers->set('Content-Disposition', 'attachment; filename="transactions.csv"');        
    
        return $response;
    }

    #[Route('/clear', name: 'app_transaction_clear', methods: ['GET', 'POST'])]
    public function clear(EntityManagerInterface $entityManager): Response
    {
        $connection = $entityManager->getConnection();
        $platform   = $connection->getDatabasePlatform();
    
        $connection->executeStatement($platform->getTruncateTableSQL('transaction', false));
    
        return $this->redirectToRoute('app_transaction_index', [], Response::HTTP_SEE_OTHER);
    }
       
}
