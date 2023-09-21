<?php

namespace App\Test\Controller;

use App\Entity\VatRate;
use App\Repository\VatRateRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class VatRateControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private VatRateRepository $repository;
    private string $path = '/vat/rate/';
    private EntityManagerInterface $manager;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->repository = static::getContainer()->get('doctrine')->getRepository(VatRate::class);

        foreach ($this->repository->findAll() as $object) {
            $this->manager->remove($object);
        }
    }

    public function testIndex(): void
    {
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('VatRate index');

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
            'vat_rate[rate]' => 'Testing',
            'vat_rate[effective_date]' => 'Testing',
            'vat_rate[createdAt]' => 'Testing',
        ]);

        self::assertResponseRedirects('/vat/rate/');

        self::assertSame($originalNumObjectsInRepository + 1, count($this->repository->findAll()));
    }

    public function testShow(): void
    {
        $this->markTestIncomplete();
        $fixture = new VatRate();
        $fixture->setRate('My Title');
        $fixture->setEffective_date('My Title');
        $fixture->setCreatedAt('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('VatRate');

        // Use assertions to check that the properties are properly displayed.
    }

    public function testEdit(): void
    {
        $this->markTestIncomplete();
        $fixture = new VatRate();
        $fixture->setRate('My Title');
        $fixture->setEffective_date('My Title');
        $fixture->setCreatedAt('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'vat_rate[rate]' => 'Something New',
            'vat_rate[effective_date]' => 'Something New',
            'vat_rate[createdAt]' => 'Something New',
        ]);

        self::assertResponseRedirects('/vat/rate/');

        $fixture = $this->repository->findAll();

        self::assertSame('Something New', $fixture[0]->getRate());
        self::assertSame('Something New', $fixture[0]->getEffective_date());
        self::assertSame('Something New', $fixture[0]->getCreatedAt());
    }

    public function testRemove(): void
    {
        $this->markTestIncomplete();

        $originalNumObjectsInRepository = count($this->repository->findAll());

        $fixture = new VatRate();
        $fixture->setRate('My Title');
        $fixture->setEffective_date('My Title');
        $fixture->setCreatedAt('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        self::assertSame($originalNumObjectsInRepository + 1, count($this->repository->findAll()));

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertSame($originalNumObjectsInRepository, count($this->repository->findAll()));
        self::assertResponseRedirects('/vat/rate/');
    }
}
