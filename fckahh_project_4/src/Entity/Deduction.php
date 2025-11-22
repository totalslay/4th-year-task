<?php

namespace App\Entity;

use App\Repository\DeductionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DeductionRepository::class)]
class Deduction
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'deductions')]
    #[ORM\JoinColumn(nullable: false)]
    private ?PayrollPeriod $period = null;

    #[ORM\ManyToOne(inversedBy: 'deductions')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Employee $employee = null;

    #[ORM\Column(length: 50)]
    private ?string $type = null;

    #[ORM\Column]
    private ?float $amount = null;

    /**
     * @var Collection<int, Adjustment>
     */
    #[ORM\OneToMany(targetEntity: Adjustment::class, mappedBy: 'deduction')]
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

    public function setPeriod(?PayrollPeriod $period): static
    {
        $this->period = $period;

        return $this;
    }

    public function getEmployee(): ?Employee
    {
        return $this->employee;
    }

    public function setEmployee(?Employee $employee): static
    {
        $this->employee = $employee;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getAmount(): ?float
    {
        return $this->amount;
    }

    public function setAmount(float $amount): static
    {
        $this->amount = $amount;

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
            $adjustment->setDeduction($this);
        }

        return $this;
    }

    public function removeAdjustment(Adjustment $adjustment): static
    {
        if ($this->adjustments->removeElement($adjustment)) {
            // set the owning side to null (unless already changed)
            if ($adjustment->getDeduction() === $this) {
                $adjustment->setDeduction(null);
            }
        }

        return $this;
    }
}
