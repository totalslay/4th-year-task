<?php

namespace App\Entity;

use App\Repository\AdjustmentRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AdjustmentRepository::class)]
class Adjustment
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'adjustments')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Employee $employee = null;

    #[ORM\ManyToOne(inversedBy: 'adjustments')]
    private ?Accrual $accrual = null;

    #[ORM\ManyToOne(inversedBy: 'adjustments')]
    private ?Deduction $deduction = null;

    #[ORM\ManyToOne(inversedBy: 'adjustments')]
    private ?SalaryCalculation $salaryCalculation = null;

    #[ORM\ManyToOne(inversedBy: 'adjustments')]
    private ?TaxRule $taxRule = null;

    #[ORM\Column(length: 100)]
    private ?string $changedFieldName = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $oldValue = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $newValue = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $changeReason = null;

    #[ORM\Column(length: 100)]
    private ?string $changedBy = null;

    #[ORM\Column]
    private ?\DateTime $changedAt = null;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getAccrual(): ?Accrual
    {
        return $this->accrual;
    }

    public function setAccrual(?Accrual $accrual): static
    {
        $this->accrual = $accrual;

        return $this;
    }

    public function getDeduction(): ?Deduction
    {
        return $this->deduction;
    }

    public function setDeduction(?Deduction $deduction): static
    {
        $this->deduction = $deduction;

        return $this;
    }

    public function getSalaryCalculation(): ?SalaryCalculation
    {
        return $this->salaryCalculation;
    }

    public function setSalaryCalculation(?SalaryCalculation $salaryCalculation): static
    {
        $this->salaryCalculation = $salaryCalculation;

        return $this;
    }

    public function getTaxRule(): ?TaxRule
    {
        return $this->taxRule;
    }

    public function setTaxRule(?TaxRule $taxRule): static
    {
        $this->taxRule = $taxRule;

        return $this;
    }

    public function getChangedFieldName(): ?string
    {
        return $this->changedFieldName;
    }

    public function setChangedFieldName(string $changedFieldName): static
    {
        $this->changedFieldName = $changedFieldName;

        return $this;
    }

    public function getOldValue(): ?string
    {
        return $this->oldValue;
    }

    public function setOldValue(string $oldValue): static
    {
        $this->oldValue = $oldValue;

        return $this;
    }

    public function getNewValue(): ?string
    {
        return $this->newValue;
    }

    public function setNewValue(string $newValue): static
    {
        $this->newValue = $newValue;

        return $this;
    }

    public function getChangeReason(): ?string
    {
        return $this->changeReason;
    }

    public function setChangeReason(string $changeReason): static
    {
        $this->changeReason = $changeReason;

        return $this;
    }

    public function getChangedBy(): ?string
    {
        return $this->changedBy;
    }

    public function setChangedBy(string $changedBy): static
    {
        $this->changedBy = $changedBy;

        return $this;
    }

    public function getChangedAt(): ?\DateTime
    {
        return $this->changedAt;
    }

    public function setChangedAt(\DateTime $changedAt): static
    {
        $this->changedAt = $changedAt;

        return $this;
    }
}
