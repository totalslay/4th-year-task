<?php

namespace App\MessageHandler;

use App\Calculations\TaxCalc;
use App\Entity\Accrual;
use App\Entity\Deduction;
use App\Message\GeneratePayslipMessage;
use App\Entity\Payslip;
use App\Entity\Employee;
use App\Entity\PayrollPeriod;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use TCPDF;

#[AsMessageHandler]
class GeneratePayslipHandler
{
    private $entityManager;
    private $pdfDirectory;
    private $taxCalc;

    public function __construct(EntityManagerInterface $entityManager, string $pdfDirectory, TaxCalc $taxCalc)
    {
        $this->entityManager = $entityManager;
        $this->pdfDirectory = $pdfDirectory;
        $this->taxCalc = $taxCalc;
    }

    public function __invoke(GeneratePayslipMessage $message)
    {
        $employee = $this->entityManager->getRepository(Employee::class)->find($message->getEmployee());
        $period = $this->entityManager->getRepository(PayrollPeriod::class)->find($message->getPeriodId());

        if (!$employee || !$period) {
            throw new \Exception('Employee or period not found');
        }

        $accruals = $this->entityManager->getRepository(Accrual::class)->findBy([
            'employee' => $employee,
            'period' => $period
        ]);

        $deductions = $this->entityManager->getRepository(Deduction::class)->findBy([
            'employee' => $employee,
            'period' => $period
        ]);

        $filename = $this->generatePdf($employee, $period, $accruals, $deductions);

        $payslip = new Payslip();
        $payslip->setEmployee($employee);
        $payslip->setPdfFilename($filename);
        $payslip->setGeneratedAt(new \DateTime());

        $this->entityManager->persist($payslip);
        $this->entityManager->flush();

        echo "PDF generated for {$employee->getFullName()} - {$filename}\n";
    }

    private function generatePdf(Employee $employee, PayrollPeriod $period, array $accruals, array $deductions): string
    {
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        $pdf->SetCreator('Payroll System');
        $pdf->SetAuthor('Payroll System');
        $pdf->SetTitle('Payslip');
        $pdf->SetSubject('Employees payslip');
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);

        $pdf->AddPage();

        $pdf->SetFont('arial', 'B', 16);
        $pdf->Cell(0, 10, 'Payslip', 0, 1, 'C');
        $pdf->Ln(10);

        $pdf->SetFont('arial', 'B', 12);
        $pdf->Cell(0, 8, 'Employees info:', 0, 1);
        
        $pdf->SetFont('arial', '', 10);
        $pdf->Cell(0, 6, 'Full name: ' . $employee->getFullName(), 0, 1);
        $pdf->Cell(0, 6, 'TIN: ' . $employee->getTIN(), 0, 1);
        $pdf->Cell(0, 6, 'Bank account: ' . $employee->getBankAccount(), 0, 1);
        $pdf->Cell(0, 6, 'Employment type: ' . $employee->getEmploymentType(), 0, 1);
        $pdf->Cell(0, 6, 'Period: ' . $period->getStartDate() . ' - ' . $period->getEndDate(), 0, 1);
        $pdf->Ln(10);

        $pdf->SetFont('arial', 'B', 12);
        $pdf->Cell(0, 8, 'Accruals:', 0, 1);
        
        $pdf->SetFont('arial', '', 10);
        $totalAccruals = 0;
        foreach ($accruals as $accrual) {
            $pdf->Cell(0, 6, '.' . $accrual->getType() . ': ' . number_format($accrual->getAmount(), 0, '.', ' '), 0, 1);
            $totalAccruals += $accrual->getAmount();
        }
        
        if (empty($accruals)) {
            $pdf->Cell(0, 6, 'No accruals', 0, 1);
        } else {
            $pdf->SetFont('arial', 'B', 10);
            $pdf->Cell(0, 6, 'Total accruals: ' . number_format($totalAccruals, 0, '.', ' '), 0, 1);
        }
        $pdf->Ln(8);

        $pdf->SetFont('arial', 'B', 12);
        $pdf->Cell(0, 8, 'Deductions:', 0, 1);
        
        $pdf->SetFont('arial', '', 10);
        $totalDeductions = 0;
        foreach ($deductions as $deduction) {
            $pdf->Cell(0, 6, '.' . $deduction->getType() . ': ' . number_format($deduction->getAmount(), 0, '.', ' '), 0, 1);
            $totalDeductions += $deduction->getAmount();
        }
        
        if (empty($deductions)) {
            $pdf->Cell(0, 6, 'No deductions', 0, 1);
        } else {
            $pdf->SetFont('arial', 'B', 10);
            $pdf->Cell(0, 6, 'Total deductions: ' . number_format($totalDeductions, 0, '.', ' '), 0, 1);
        }
        $pdf->Ln(8);

        $pdf->setFont('arial', 'B', 12);
        $pdf->Cell(0, 8, 'Analytics', 0, 1);
        $pdf->setFont('arial', '', 10);

        $grossSalary = $totalAccruals;
        $progressiveTax = $this->taxCalc->calcSalaryTax($grossSalary);
        $pdf->Cell(0, 6, 'Progressive tax: ' . number_format($progressiveTax, 0, '.', ' '), 0, 1);

        $otherDeductions = $totalDeductions - $progressiveTax;
        $pdf->Cell(0, 6, 'Other deductions: ' . number_format($otherDeductions, 0, '.', ' '), 0, 1);
        $pdf->Ln(8);

        $pdf->SetFont('arial', 'B', 14);
        $netSalary = $totalAccruals - $totalDeductions;
        $pdf->Cell(0, 10, 'Total payment: ' . number_format($netSalary, 0, '.', ' '), 0, 1, 'C');
        $pdf->Ln(10);

        $pdf->SetFont('arial', '', 10);
        $pdf->Cell(0, 6, 'Generated at: ' . date('d.m.Y H:i'), 0, 1);
        $pdf->Ln(10);

        if (!is_dir($this->pdfDirectory)) {
            mkdir($this->pdfDirectory, 0777, true);
        }

        $filename = sprintf(
            'payslip_%s_%s_%s.pdf',
            $employee->getId(),
            $period->getId(), 
            date('Y-m-d_H-i-s')
        );

        $filepath = $this->pdfDirectory . '/' . $filename;
        $pdf->Output($filepath, 'F');

        return $filename;
    }
}