<?php

namespace App\Calculations;

use App\Repository\AccrualRepository;
use App\Repository\DeductionRepository;
 class ProportionCalc
 {
    private $accrualRep;
    private $deductionRep;

    public function __construct(
        AccrualRepository $accrualRep,
        DeductionRepository $deductionRep
    ){
        $this->accrualRep = $accrualRep;
        $this->deductionRep = $deductionRep;
    }

    public function getAccruals(string $type, int $periodId):array
    {
        return $this->accrualRep->findByTypeAndPeriod($type, $periodId);
    }

    public function getDeductions(string $type, int $periodId):array
    {
        return $this->deductionRep->findDeductions($type, $periodId);
    }

    public function calcProportionalAccrual(float $baseAmount, string $employmentType, float $workedDays, float $totalDays):float
    {
        $ratio = $workedDays/$totalDays;
        if ($employmentType === 'PART_TIME') {
            $ratio *= 0.5; //типа полставки
        }
        return $baseAmount * $ratio;
    }
 }