<?php

namespace App\Controller;

use App\Entity\VatRate;
use App\Form\VatRateType;
use App\Entity\Transaction;
use App\Repository\VatRateRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/vat/rate')]
class VatRateController extends AbstractController
{
    #[Route('/', name: 'app_vat_rate_index', methods: ['GET'])]
    public function index(VatRateRepository $vatRateRepository): Response
    {
        return $this->render('vat_rate/index.html.twig', [
            'vat_rates' => $vatRateRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_vat_rate_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $vatRate = new VatRate();
        $form = $this->createForm(VatRateType::class, $vatRate);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $vatRateRepository = $entityManager->getRepository(VatRate::class);
            $vatRateCount = $vatRateRepository->count([]);

            if($vatRateCount===0) {
                // check if new vat rate is earlier than today
                $effectiveDate = $vatRate->getEffectiveDate();
                $today = new \DateTime();

                if($effectiveDate > $today) {
                    $this->addFlash('error', 'First VAT rate effective date cannot be in the future');
                    return $this->redirectToRoute('app_vat_rate_new');
                }                
            }
          
            $vatRate->setCreatedAt(new \DateTime());
            $inputPercentage = $vatRate->getRate();
            $decimalPercentage = $inputPercentage / 100;
            $vatRate->setRate($decimalPercentage);
            $entityManager->persist($vatRate);
            $entityManager->flush();

            return $this->redirectToRoute('app_vat_rate_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('vat_rate/new.html.twig', [
            'vat_rate' => $vatRate,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_vat_rate_show', methods: ['GET'])]
    public function show(VatRate $vatRate): Response
    {
        return $this->render('vat_rate/show.html.twig', [
            'vat_rate' => $vatRate,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_vat_rate_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, VatRate $vatRate, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(VatRateType::class, $vatRate);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_vat_rate_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('vat_rate/edit.html.twig', [
            'vat_rate' => $vatRate,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_vat_rate_delete', methods: ['POST'])]
    public function delete(Request $request, VatRate $vatRate, EntityManagerInterface $entityManager): Response
    {

        // check for related transaction records
        $transactionRepository = $entityManager->getRepository(Transaction::class);
        $transactionCount = $transactionRepository->count(['vat_rate' => $vatRate]);
        if($transactionCount > 0) {
            $this->addFlash('error', 'Cannot delete VAT rate with related transactions');
            return $this->redirectToRoute('app_vat_rate_index');
        }

        if ($this->isCsrfTokenValid('delete'.$vatRate->getId(), $request->request->get('_token'))) {
            $entityManager->remove($vatRate);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_vat_rate_index', [], Response::HTTP_SEE_OTHER);
    }
}
