<?php

namespace App\Entity;

use App\Repository\TransactionRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TransactionRepository::class)]
#[ORM\HasLifecycleCallbacks()]

class Transaction
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 4)]
    private ?string $amount = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 4)]
    private ?string $vatAmountExVat = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 4)]
    private ?string $amountExVat = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 4)]
    private ?string $vatAmountIncVat = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 4)]
    private ?string $amountIncVat = null;

    #[ORM\Column]
    private ?\DateTime $createdAt = null;

    #[ORM\ManyToOne(inversedBy: 'transaction_id')]
    #[ORM\JoinColumn(nullable: false)]
    private ?VatRate $vat_rate = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAmount(): ?string
    {
        return $this->amount;
    }

    public function setAmount(string $amount): static
    {
        $this->amount = $amount;

        return $this;
    }

    public function getVatAmountExVat(): ?string
    {
        return $this->vatAmountExVat;
    }

    public function setVatAmountExVat(string $vatAmountExVat): static
    {
        $this->vatAmountExVat = $vatAmountExVat;

        return $this;
    }

    public function getAmountExVat(): ?string
    {
        return $this->amountExVat;
    }

    public function setAmountExVat(string $amountExVat): static
    {
        $this->amountExVat = $amountExVat;

        return $this;
    }
    
    public function getVatAmountIncVat(): ?string
    {
        return $this->vatAmountIncVat;
    }

    public function setVatAmountIncVat(string $vatAmountIncVat): static
    {
        $this->vatAmountIncVat = $vatAmountIncVat;

        return $this;
    }
    
    public function getAmountIncVat(): ?string
    {
        return $this->amountIncVat;
    }

    public function setAmountIncVat(string $amountIncVat): static
    {
        $this->amountIncVat = $amountIncVat;

        return $this;
    }
    
    public function getCreatedAt(): ?\DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?\DateTime $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getVatRate(): ?VatRate
    {
        return $this->vat_rate;
    }

    public function setVatRate(?VatRate $vat_rate): static
    {
        $this->vat_rate = $vat_rate;

        return $this;
    }
    
}
