<?php

namespace App\Entity;

use App\Repository\PayrollPeriodRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PayrollPeriodRepository::class)]
class PayrollPeriod
{
    public const STATUS_DRAFT = 'DRAFT';
    public const STATUS_APPROVED = 'APPROVED';
    public const STATUS_PROCESSED = 'PROCESSED';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTime $startDate = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTime $endDate = null;

    #[ORM\Column(length: 20)]
    private ?string $status = 'DRAFT';

    /**
     * @var Collection<int, Accrual>
     */
    #[ORM\OneToMany(targetEntity: Accrual::class, mappedBy: 'period')]
    private Collection $accruals;

    /**
     * @var Collection<int, Deduction>
     */
    #[ORM\OneToMany(targetEntity: Deduction::class, mappedBy: 'period')]
    private Collection $deductions;

    public function __construct()
    {
        $this->accruals = new ArrayCollection();
        $this->deductions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStartDate(): ?\DateTime
    {
        return $this->startDate;
    }

    public function setStartDate(\DateTime $startDate): static
    {
        $this->startDate = $startDate;

        return $this;
    }

    public function getEndDate(): ?\DateTime
    {
        return $this->endDate;
    }

    public function setEndDate(\DateTime $endDate): static
    {
        $this->endDate = $endDate;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;

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
            $accrual->setPeriod($this);
        }

        return $this;
    }

    public function removeAccrual(Accrual $accrual): static
    {
        if ($this->accruals->removeElement($accrual)) {
            // set the owning side to null (unless already changed)
            if ($accrual->getPeriod() === $this) {
                $accrual->setPeriod(null);
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
            $deduction->setPeriod($this);
        }

        return $this;
    }

    public function removeDeduction(Deduction $deduction): static
    {
        if ($this->deductions->removeElement($deduction)) {
            // set the owning side to null (unless already changed)
            if ($deduction->getPeriod() === $this) {
                $deduction->setPeriod(null);
            }
        }

        return $this;
    }

    public function approve():void
    {
        if($this->status !== self::STATUS_DRAFT) {
            throw new \RuntimeException('Only draft periods can be approved');
        }
        $this->status = self::STATUS_APPROVED;
    }

    public function process():void
    {
        if($this->status !== self::STATUS_APPROVED) {
            throw new \RuntimeException('Only approved periods can be processed');
        }
        $this->status = self::STATUS_PROCESSED;
    }

    public static function getAvailableStatuses():array
    {
        return[
            self::STATUS_DRAFT => 'Draft',
            self::STATUS_APPROVED => 'Approved',
            self::STATUS_PROCESSED => 'Processed',
        ];
    }

    public function canApprove():bool
    {
        return $this->status === self::STATUS_DRAFT;
    }

    public function canProcess():bool
    {
        return $this->status === self::STATUS_APPROVED;
    }

    public function canEdit():bool
    {
        return $this->status === self::STATUS_DRAFT;
    }

    public function canDelete():bool
    {
        return $this->status === self::STATUS_DRAFT;
    }

}
