<?php

namespace App\Service;

use Psr\Log\LoggerInterface;

class Logs
{
    private LoggerInterface $logger;

    public function __construct(LoggerInterface $auditLogger)
    {
        $this->logger = $auditLogger;
    }

    public function logAdjustment(
        string $action,
        string $changedBy,
        ?string $emplyeeName = null,
        ?string $fieldName = null,
        ?string $oldValue = null,
        ?string $newValue = null,
        ?string $changeReason =  null
    ): void {
        $logData = [
            'action' => $action,
            'changed_by' => $changedBy,
            'employee' => $emplyeeName,
            'field' => $fieldName,
            'old_value' => $oldValue,
            'new_value' =>$newValue,
            'reason' => $changeReason,
            'timestamp' => date('Y-m-d H:i:s')
        ];
        $this->logger->info('Manual adjustment', $logData);
    }
}
