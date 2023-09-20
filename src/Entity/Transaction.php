<?php

namespace App\Entity;

use App\Repository\TransactionRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TransactionRepository::class)]
class Transaction
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 4)]
    private ?string $amount = null;

    #[ORM\Column]
    private ?bool $requires_vat = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 4)]
    private ?string $gross = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

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

    public function isRequiresVat(): ?bool
    {
        return $this->requires_vat;
    }

    public function setRequiresVat(bool $requires_vat): static
    {
        $this->requires_vat = $requires_vat;

        return $this;
    }

    public function getGross(): ?string
    {
        return $this->gross;
    }

    public function setGross(string $gross): static
    {
        $this->gross = $gross;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
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
