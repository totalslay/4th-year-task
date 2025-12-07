<?php

namespace App\Message;

class GeneratePayslipMessage
{
    private int $employeeId;
    private int $periodId;

    public function __construct(int $employeeId, int $periodId)
    {
        $this->employeeId = $employeeId;
        $this->periodId = $periodId;
    }

    public function getEmployeeId(): int
    {
        return $this->employeeId;
    }

    public function getPeriodId(): int
    {
        return $this->periodId;
    }
}
