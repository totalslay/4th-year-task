<?php

namespace App\Entity;

use App\Repository\SalaryCalculationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SalaryCalculationRepository::class)]
class SalaryCalculation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?PayrollPeriod $period = null;

    #[ORM\Column]
    private ?float $grossAmount = null;

    #[ORM\Column]
    private ?float $netAmount = null;

    /**
     * @var Collection<int, Adjustment>
     */
    #[ORM\OneToMany(targetEntity: Adjustment::class, mappedBy: 'salaryCalculation')]
    private Collection $adjustments;

    public function __construct()
    {
        $this->adjustments = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPeriod(): ?PayrollPeriod
    {
        return $this->period;
    }

    public function setPeriod(PayrollPeriod $period): static
    {
        $this->period = $period;

        return $this;
    }

    public function getGrossAmount(): ?float
    {
        return $this->grossAmount;
    }

    public function setGrossAmount(float $grossAmount): static
    {
        $this->grossAmount = $grossAmount;

        return $this;
    }

    public function getNetAmount(): ?float
    {
        return $this->netAmount;
    }

    public function setNetAmount(float $netAmount): static
    {
        $this->netAmount = $netAmount;

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
            $adjustment->setSalaryCalculation($this);
        }

        return $this;
    }

    public function removeAdjustment(Adjustment $adjustment): static
    {
        if ($this->adjustments->removeElement($adjustment)) {
            // set the owning side to null (unless already changed)
            if ($adjustment->getSalaryCalculation() === $this) {
                $adjustment->setSalaryCalculation(null);
            }
        }

        return $this;
    }
}
