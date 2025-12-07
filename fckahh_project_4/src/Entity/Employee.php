<?php

namespace App\Entity;

use App\Repository\EmployeeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EmployeeRepository::class)]
class Employee
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $fullName = null;

    #[ORM\Column(length: 12)]
    private ?string $TIN = null;

    #[ORM\Column(length: 20)]
    private ?string $bankAccount = null;

    #[ORM\Column(length: 20)]
    private ?string $employmentType = null;

    /**
     * @var Collection<int, Accrual>
     */
    #[ORM\OneToMany(targetEntity: Accrual::class, mappedBy: 'employee')]
    private Collection $accruals;

    /**
     * @var Collection<int, Deduction>
     */
    #[ORM\OneToMany(targetEntity: Deduction::class, mappedBy: 'employee')]
    private Collection $deductions;

    /**
     * @var Collection<int, Payslip>
     */
    #[ORM\OneToMany(targetEntity: Payslip::class, mappedBy: 'employee')]
    private Collection $payslips;

    /**
     * @var Collection<int, Adjustment>
     */
    #[ORM\OneToMany(targetEntity: Adjustment::class, mappedBy: 'employee')]
    private Collection $adjustments;

    public function __construct()
    {
        $this->accruals = new ArrayCollection();
        $this->deductions = new ArrayCollection();
        $this->payslips = new ArrayCollection();
        $this->adjustments = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFullName(): ?string
    {
        return $this->fullName;
    }

    public function setFullName(string $fullName): static
    {
        $this->fullName = $fullName;

        return $this;
    }

    public function getTIN(): ?string
    {
        return $this->TIN;
    }

    public function setTIN(string $TIN): static
    {
        $this->TIN = $TIN;

        return $this;
    }

    public function getBankAccount(): ?string
    {
        return $this->bankAccount;
    }

    public function setBankAccount(string $bankAccount): static
    {
        $this->bankAccount = $bankAccount;

        return $this;
    }

    public function getEmploymentType(): ?string
    {
        return $this->employmentType;
    }

    public function setEmploymentType(string $employmentType): static
    {
        $this->employmentType = $employmentType;

        return $this;
    }

    /**
     * @return Collection<int, Accrual>
     */
    public function getAccruals(): Collection
    {
        return $this->accruals;
    }

    public function addAccrual(Accrual $accrual): static
    {
        if (!$this->accruals->contains($accrual)) {
            $this->accruals->add($accrual);
            $accrual->setEmployee($this);
        }

        return $this;
    }

    public function removeAccrual(Accrual $accrual): static
    {
        if ($this->accruals->removeElement($accrual)) {
            // set the owning side to null (unless already changed)
            if ($accrual->getEmployee() === $this) {
                $accrual->setEmployee(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Deduction>
     */
    public function getDeductions(): Collection
    {
        return $this->deductions;
    }

    public function addDeduction(Deduction $deduction): static
    {
        if (!$this->deductions->contains($deduction)) {
            $this->deductions->add($deduction);
            $deduction->setEmployee($this);
        }

        return $this;
    }

    public function removeDeduction(Deduction $deduction): static
    {
        if ($this->deductions->removeElement($deduction)) {
            // set the owning side to null (unless already changed)
            if ($deduction->getEmployee() === $this) {
                $deduction->setEmployee(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Payslip>
     */
    public function getPayslips(): Collection
    {
        return $this->payslips;
    }

    public function addPayslip(Payslip $payslip): static
    {
        if (!$this->payslips->contains($payslip)) {
            $this->payslips->add($payslip);
            $payslip->setEmployee($this);
        }

        return $this;
    }

    public function removePayslip(Payslip $payslip): static
    {
        if ($this->payslips->removeElement($payslip)) {
            // set the owning side to null (unless already changed)
            if ($payslip->getEmployee() === $this) {
                $payslip->setEmployee(null);
            }
        }

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
            $adjustment->setEmployee($this);
        }

        return $this;
    }

    public function removeAdjustment(Adjustment $adjustment): static
    {
        if ($this->adjustments->removeElement($adjustment)) {
            // set the owning side to null (unless already changed)
            if ($adjustment->getEmployee() === $this) {
                $adjustment->setEmployee(null);
            }
        }

        return $this;
    }
}
