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
        return $this->accrualRep->findBy([
            'type' => $type,
            'period' => $periodId
        ],['amount' => "DESC"]);
    }

    public function getDeductions(string $type, int $periodId):array
    {
        return $this->deductionRep->findDeductions($type, $periodId);
    }
 }