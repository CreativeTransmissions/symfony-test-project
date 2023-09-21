<?php

namespace App\Test\Controller;

use App\Entity\Transaction;
use App\Repository\TransactionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TransactionControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private TransactionRepository $repository;
    private string $path = '/transaction/';
    private EntityManagerInterface $manager;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->repository = static::getContainer()->get('doctrine')->getRepository(Transaction::class);

        foreach ($this->repository->findAll() as $object) {
            $this->manager->remove($object);
        }
    }

    public function testIndex(): void
    {
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Transaction index');

        // Use the $crawler to perform additional assertions e.g.
        // self::assertSame('Some text on the page', $crawler->filter('.p')->first());
    }

    public function testNew(): void
    {
        $originalNumObjectsInRepository = count($this->repository->findAll());

        $this->markTestIncomplete();
        $this->client->request('GET', sprintf('%snew', $this->path));

        self::assertResponseStatusCodeSame(200);

        $this->client->submitForm('Save', [
            'transaction[amount]' => 'Testing',
            'transaction[gross]' => 'Testing',
            'transaction[createdAt]' => 'Testing',
            'transaction[vat_rate]' => 'Testing',
        ]);

        self::assertResponseRedirects('/transaction/');

        self::assertSame($originalNumObjectsInRepository + 1, count($this->repository->findAll()));
    }

    public function testShow(): void
    {
        $this->markTestIncomplete();
        $fixture = new Transaction();
        $fixture->setAmount('My Title');
        $fixture->setCreatedAt('My Title');
        $fixture->setVat_rate('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Transaction');

        // Use assertions to check that the properties are properly displayed.
    }

    public function testEdit(): void
    {
        $this->markTestIncomplete();
        $fixture = new Transaction();
        $fixture->setAmount('My Title');
        $fixture->setGross('My Title');
        $fixture->setCreatedAt('My Title');
        $fixture->setVat_rate('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'transaction[amount]' => 'Something New',
            'transaction[gross]' => 'Something New',
            'transaction[createdAt]' => 'Something New',
            'transaction[vat_rate]' => 'Something New',
        ]);

        self::assertResponseRedirects('/transaction/');

        $fixture = $this->repository->findAll();

        self::assertSame('Something New', $fixture[0]->getAmount());
        self::assertSame('Something New', $fixture[0]->getGross());
        self::assertSame('Something New', $fixture[0]->getCreatedAt());
        self::assertSame('Something New', $fixture[0]->getVat_rate());
    }

    public function testRemove(): void
    {
        $this->markTestIncomplete();

        $originalNumObjectsInRepository = count($this->repository->findAll());

        $fixture = new Transaction();
        $fixture->setAmount('My Title');
        $fixture->setGross('My Title');
        $fixture->setCreatedAt('My Title');
        $fixture->setVat_rate('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        self::assertSame($originalNumObjectsInRepository + 1, count($this->repository->findAll()));

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertSame($originalNumObjectsInRepository, count($this->repository->findAll()));
        self::assertResponseRedirects('/transaction/');
    }
}
