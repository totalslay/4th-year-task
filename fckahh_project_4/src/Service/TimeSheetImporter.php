<?php

namespace App\Service;

use App\Entity\Accrual;
use App\Entity\Employee;
use App\Entity\PayrollPeriod;
use Doctrine\ORM\EntityManagerInterface;

class TimeSheetImporter
{
    private $entityManager;

     public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function importTimeSheet(string $csvContent, PayrollPeriod $period):array
    {
        $errors = [];
        $successCount = 0;

        $lines = explode("\n", trim($csvContent));
        array_shift($lines);

        foreach ($lines as $lineNumber => $line) {
            $data = str_getcsv(trim($line), ';');
            if (count($data) < 3) {
                $errors[] = "Line" . ($lineNumber + 2) . "Wrong data format";
                continue;
            }

            [$tin, $workedHours, $overtimeHours] = $data;

            $employee = $this->entityManager->getRepository(Employee::class)->findOneBy(["TIN" => $tin]);
            if (!$employee) {
                $errors[] = "Line" . ($lineNumber + 2) . "Employees TIN not found";
                continue;
            }

            try {
                if ($workedHours > 0) {
                    $this->createAccrual($employee, $period, 'SALARY', (float)$workedHours * 2000);
                }
                if ($overtimeHours > 0) {
                    $this->createAccrual($employee, $period, 'OVERTIME', (float)$overtimeHours * 3000);
                }
                $successCount++;
            } catch (\Exception $e) {
                $errors[] = "Line " . ($lineNumber + 2) . ": Error - " . $e->getMessage();
            }
        }

        $this->entityManager->flush();
        return [
            'success' => $successCount,
            'errors' => $errors
        ];
    }

    private function createAccrual(Employee $employee, PayrollPeriod $period, string $type, float $amount):void
    {
        $existing = $this->entityManager->getRepository(Accrual::class)->findOneBy([
            'employee' => $employee,
            'period' => $period,
            'type' => $type
        ]);
        
        if ($existing) {
            $existing->setAmount($existing->getAmount() + $amount);
        } else {
            $accrual = new Accrual();
            $accrual->setEmployee($employee);
            $accrual->setPeriod($period);
            $accrual->setType($type);
            $accrual->setAmount($amount);
            $this->entityManager->persist($accrual);
        }
    }
}