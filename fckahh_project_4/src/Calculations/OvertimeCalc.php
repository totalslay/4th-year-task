<?php

namespace App\Calculations;

use App\Repository\AccrualRepository;

class OvertimeCalc
{
    private $accrualRep;

    public function __construct(AccrualRepository $accrualRep)
    {
        $this->accrualRep = $accrualRep;
    }

    public function getOvertimeByPeriod(int $periodId): array
    {
        return $this->accrualRep->findOvertimeByPeriod($periodId);
    }

    public function getHighOvertimeEmployees(int $periodId): array
    {
        return $this->accrualRep->findEmployeesOvertime($periodId);
    }
}
