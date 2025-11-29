<?php

namespace App\Message;

class GeneratePayrollReportMessage
{
    private int $periodId;

    public function __construct(int $periodId)
    {
        $this->periodId = $periodId;
    }

    public function getPeriodId(): int
    {
        return $this->periodId;
    }
}