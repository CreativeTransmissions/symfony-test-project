<?php

namespace App\Entity;

use App\Repository\VatRateRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: VatRateRepository::class)]
#[ORM\HasLifecycleCallbacks()]

class VatRate
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 5, scale: 2)]
    #[Assert\NotBlank]
    #[Assert\NotNull]
    #[Assert\Positive]    
    private ?string $rate = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $effective_date = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\OneToMany(mappedBy: 'vat_rate', targetEntity: Transaction::class)]
    private Collection $transaction_id;

    public function __construct()
    {
        $this->transaction_id = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getRate(): ?string
    {
        return $this->rate;
    }

    public function setRate(string $rate): static
    {
        $this->rate = $rate;

        return $this;
    }

    public function getEffectiveDate(): ?\DateTimeInterface
    {
        return $this->effective_date;
    }

    public function setEffectiveDate(\DateTimeInterface $effective_date): static
    {
        $this->effective_date = $effective_date;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return Collection<int, Transaction>
     */
    public function getTransactionId(): Collection
    {
        return $this->transaction_id;
    }

    public function addTransactionId(Transaction $transactionId): static
    {
        if (!$this->transaction_id->contains($transactionId)) {
            $this->transaction_id->add($transactionId);
            $transactionId->setVatRate($this);
        }

        return $this;
    }

    public function removeTransactionId(Transaction $transactionId): static
    {
        if ($this->transaction_id->removeElement($transactionId)) {
            // set the owning side to null (unless already changed)
            if ($transactionId->getVatRate() === $this) {
                $transactionId->setVatRate(null);
            }
        }

        return $this;
    }
}
