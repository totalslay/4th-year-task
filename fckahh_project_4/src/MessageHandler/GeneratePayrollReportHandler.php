<?php

namespace App\MessageHandler;

use App\Message\GeneratePayrollReportMessage;
use App\Calculations\OvertimeCalc;
use App\Calculations\ProportionCalc;
use App\Calculations\TaxCalc;
use App\Entity\PayrollPeriod;
use App\Entity\Employee;
use App\Entity\Accrual;
use App\Entity\Deduction;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use TCPDF;

#[AsMessageHandler]
class GeneratePayrollReportHandler
{
    private $entityManager;
    private $pdfDirectory;
    private $taxCalc;
    private $overtimeCalc;
    private $proportionCalc;

    public function __construct(EntityManagerInterface $entityManager, string $pdfDirectory, TaxCalc $taxCalc, OvertimeCalc $overtimeCalc, ProportionCalc $proportionCalc)
    {
        $this->entityManager = $entityManager;
        $this->pdfDirectory = $pdfDirectory;
        $this->taxCalc = $taxCalc;
        $this->overtimeCalc = $overtimeCalc;
        $this->proportionCalc = $proportionCalc;
    }

    public function __invoke(GeneratePayrollReportMessage $message)
    {
        $period = $this->entityManager->getRepository(PayrollPeriod::class)->find($message->getPeriodId());
        $employees = $this->entityManager->getRepository(Employee::class)->findBy(['isActive' => true]);

        if (!$period) {
            throw new \Exception('Period not found');
        }

        $filename = $this->generatePayrollReport($period, $employees);
        echo "Payroll report generated for period {$period->getId()} - {$filename}\n";
    }

    private function generatePayrollReport(PayrollPeriod $period, array $employees):string
    {
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        $pdf->setCreator('Payroll System');
        $pdf->setAuthor('Payroll System');
        $pdf->setTitle('Paysheet');
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        $pdf->AddPage();

        $pdf->setFont('arial', 'B', 16);
        $pdf->Cell(0, 10, 'PAYSHEET', 0, 1, 'C');
        $pdf->Ln(5);
        $pdf->setFont('arial', '', 12);
        $pdf->Cell(0, 8, 'Period: ' . $period->getStartDate() . '-' . $period->getEndDate(), 0, 1, 'C');
        $pdf->Ln(10);

        $pdf->setFont('arial', 'B', 12);
        $pdf->Cell(0, 8, 'Period analysis:', 0, 1);
        $pdf->setFont('arial', '', 10);
        $overtimes = $this->overtimeCalc->getOvertimeByPeriod($period->getId());
        $totalOvertime = array_sum(array_column($overtimes, 'totalOvertime'));
        $pdf->Cell(0, 6, 'Total overtime:' . number_format($totalOvertime, 0, '.', ''), 0, 1);
        $highOvertimes = $this->overtimeCalc->getHighOvertimeEmployees($period->getId());
        $pdf->Cell(0, 6, 'Employees with above-average overtime:' . count($highOvertimes), 0, 1);
        $pdf->Ln(10);

        $pdf->setFont('arial', 'B', 10);
        $pdf->Cell(60, 8, 'Employee', 1, 0, 'C');
        $pdf->Cell(30, 8, 'TIN', 1, 0, 'C');
        $pdf->Cell(50, 8, 'Bank account', 1, 0, 'C');
        $pdf->Cell(30, 8, 'Accrual', 1, 0, 'C');
        $pdf->Cell(30, 8, 'Taxes', 1, 0, 'C');
        $pdf->Cell(30, 8, 'Total payment', 1, 1, 'C');

        $pdf->setFont('arial', '', 9);
        $totalSalary = 0;
        $totalTaxes = 0;
        
        foreach ($employees as $employee) {
            $salaryData = $this->calculateEmployeeSalary($employee, $period);
            $totalSalary += $salaryData['total_salary'];
            $totalTaxes += $salaryData['tax'];

            $pdf->Cell(50, 8, $this->shortName($employee->getFullName()), 1);
            $pdf->Cell(25, 8, $employee->getTIN(), 1);
            $pdf->Cell(40, 8, '....' . substr($employee->getBankAccount(), -4), 1);
            $pdf->Cell(25, 8, number_format($salaryData['gross_salary'], 0, '.', ' '), 1, 0, 'R');
            $pdf->Cell(25, 8, number_format($salaryData['tax'], 0, '.', ''), 1, 0, 'R');
            $pdf->Cell(25, 8, number_format($salaryData['net_salary'], 0, '.', ' '), 1, 1, 'R');
        }

        $pdf->SetFont('arial', 'B', 10);
        $pdf->Cell(140, 8, 'Total taxes:', 1, 0, 'R');
        $pdf->Cell(25, 8, number_format($totalTaxes, 0, '.', ' '), 1, 1, 'R');
        
        $pdf->Cell(140, 8, 'Total payment:', 1, 0, 'R');
        $pdf->Cell(25, 8, number_format($totalSalary, 0, '.', ' '), 1, 1, 'R');

        $pdf->Ln(10);
        $pdf->SetFont('arial', '', 10);
        $pdf->Cell(0, 6, 'Creation date: ' . date('d.m.Y H:i'), 0, 1);
        $pdf->Cell(0, 6, 'Number of employees: ' . count($employees), 0, 1);
        $pdf->Cell(0, 6, 'Progressive tax scale has been used', 0, 1);

        $filename = 'payroll_report_' . $period->getId() . '_' . date('Y-m-d_H-i-s') . '.pdf';
        $filepath = $this->pdfDirectory . '/' . $filename;
        $pdf->Output($filepath, 'F');

        return $filename;
    }

    private function calculateEmployeeSalary(Employee $employee, PayrollPeriod $period):array
    {
        $accruals = $this->entityManager->getRepository(Accrual::class)->findBy([
            'employee' => $employee,
            'period' => $period
        ]);

        $deductions = $this->entityManager->getRepository(Deduction::class)->findBy([
            'employee' => $employee,
            'period' => $period
        ]);

        $grossSalary = array_sum(array_map(fn($a) => $a->getAmount(), $accruals));
        $tax = $this->taxCalc->calcSalaryTax($grossSalary);
        $otherDeductions = array_sum(array_map(fn($d) => $d->getAmount(), $deductions));
        $netSalary = $grossSalary - $tax - $otherDeductions;

        return [
            'gross_salary' => $grossSalary,
            'tax' => $tax,
            'net_salary' => $netSalary
        ];
    }

    private function shortName(string $fullName): string
    {
        $parts = explode(' ', $fullName);
        if (count($parts) >= 3) {
            return $parts[0] . ' ' . mb_substr($parts[1], 0, 1) . '. ' . mb_substr($parts[2], 0, 1) . '.';
        }
        return $fullName;
    }
}