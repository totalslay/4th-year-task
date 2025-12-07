<?php

namespace App\Service;

use App\Entity\PayrollPeriod;
use App\Repository\AccrualRepository;
use App\Repository\DeductionRepository;
use App\Repository\EmployeeRepository;

class TaxReportExporter
{
    private $employeeRep;
    private $accrualRep;
    private $deductionRep;

    public function __construct(EmployeeRepository $employeeRep, AccrualRepository $accrualRep, DeductionRepository $deductionRep)
    {
        $this->employeeRep = $employeeRep;
        $this->accrualRep = $accrualRep;
        $this->deductionRep = $deductionRep;
    }

    public function exportTaxReport(PayrollPeriod $period): string
    {
        $employees = $this->employeeRep->findAll();
        $csvData = [];
        $csvData[] = [
            'TIN',
            'Full name',
            'Total accruals',
            'Tax',
            'Pension tax',
            'Other deductions',
            'Taxable base',
        ];

        foreach ($employees as $employee) {
            $accruals = $this->accrualRep->findBy([
                'employee' => $employee,
                'period' => $period,
            ]);
            $deductions = $this->deductionRep->findBy([
                'employee' => $employee,
                'period' => $period,
            ]);

            $totalAccruals = array_sum(array_map(fn ($a) => $a->getAmount(), $accruals));
            $totalDeductions = array_sum(array_map(fn ($d) => $d->getAmount(), $deductions));
            $taxDeductions = array_filter($deductions, fn ($d) => 'INCOME_TAX' === $d->getType());
            $pensionDeductions = array_filter($deductions, fn ($d) => 'PENSION' === $d->getType());
            $otherDeductions = array_filter($deductions, fn ($d) => !\in_array($d->getType(), ['INCOME_TAX', 'PENSION'], true));
            $taxAmount = array_sum(array_map(fn ($d) => $d->getAmount(), $taxDeductions));
            $pensionAmount = array_sum(array_map(fn ($d) => $d->getAmount(), $pensionDeductions));
            $otherAmount = array_sum(array_map(fn ($d) => $d->getAmount(), $otherDeductions));

            $csvData[] = [
                $employee->getTIN(),
                $employee->getFullName(),
                number_format($totalAccruals, 2, '.', ''),
                number_format($taxAmount, 2, '.', ''),
                number_format($pensionAmount, 2, '.', ''),
                number_format($otherAmount, 2, '.', ''),
                number_format($totalAccruals - $pensionAmount, 2, '.', ''),
            ];
        }

        return $this->arrayToCsv($csvData);
    }

    private function arrayToCsv(array $data): string
    {
        $output = fopen('php://temp', 'r+');
        fwrite($output, "\xEF\xBB\xBF");
        foreach ($data as $row) {
            fputcsv($output, $row, ';');
        }

        rewind($output);
        $csv = stream_get_contents($output);
        fclose($output);

        return $csv;
    }
}
