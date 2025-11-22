<?php

namespace App\Entity;

use App\Repository\TaxRuleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TaxRuleRepository::class)]
class TaxRule
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?float $minAmount = null;

    #[ORM\Column(nullable: true)]
    private ?float $maxAmount = null;

    #[ORM\Column]
    private ?float $rate = null;

    #[ORM\Column(length: 50)]
    private ?string $taxType = null;

    /**
     * @var Collection<int, Adjustment>
     */
    #[ORM\OneToMany(targetEntity: Adjustment::class, mappedBy: 'taxRule')]
    private Collection $adjustments;

    public function __construct()
    {
        $this->adjustments = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMinAmount(): ?float
    {
        return $this->minAmount;
    }

    public function setMinAmount(float $minAmount): static
    {
        $this->minAmount = $minAmount;

        return $this;
    }

    public function getMaxAmount(): ?float
    {
        return $this->maxAmount;
    }

    public function setMaxAmount(?float $maxAmount): static
    {
        $this->maxAmount = $maxAmount;

        return $this;
    }

    public function getRate(): ?float
    {
        return $this->rate;
    }

    public function setRate(float $rate): static
    {
        $this->rate = $rate;

        return $this;
    }

    public function getTaxType(): ?string
    {
        return $this->taxType;
    }

    public function setTaxType(string $taxType): static
    {
        $this->taxType = $taxType;

        return $this;
    }

    /**
     * @return Collection<int, Adjustment>
     */
    public function getAdjustments(): Collection
    {
        return $this->adjustments;
    }

    public function addAdjustment(Adjustment $adjustment): static
    {
        if (!$this->adjustments->contains($adjustment)) {
            $this->adjustments->add($adjustment);
            $adjustment->setTaxRule($this);
        }

        return $this;
    }

    public function removeAdjustment(Adjustment $adjustment): static
    {
        if ($this->adjustments->removeElement($adjustment)) {
            // set the owning side to null (unless already changed)
            if ($adjustment->getTaxRule() === $this) {
                $adjustment->setTaxRule(null);
            }
        }

        return $this;
    }
}
