<?php

namespace App\Controller;

use App\Message\GeneratePayrollReportMessage;
use App\Message\GeneratePayslipMessage;
use App\Repository\EmployeeRepository;
use App\Repository\PayrollPeriodRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;

final class AsyncTasksController extends AbstractController
{
    #[Route('/async/tasks', name: 'app_async_tasks')]
    public function index(PayrollPeriodRepository $periodRep, EmployeeRepository $employeeRep): Response
    {
        $periods = $periodRep->findAll();
        $employees = $employeeRep->findAll();

        return $this->render('async_tasks/index.html.twig', [
            'periods' => $periods,
            'employee_count' => \count($employees),
            'period_count' => \count($periods),
        ]);
    }

    #[Route('/async/generate-all-payslips/{periodId}', name: 'app_async_generate_all_payslips')]
    public function generatateAllPayslips(int $periodId, EmployeeRepository $employeeRep, MessageBusInterface $messageBus): Response
    {
        $employees = $employeeRep->findAll();
        foreach ($employees as $employee) {
            $message = new GeneratePayslipMessage($employee->getId(), $periodId);
            $messageBus->dispatch($message);
        }

        $this->addFlash('success', 'Asynchronous generation of'.\count($employees).'payslips has been started');

        return $this->redirectToRoute('app_async_tasks');
    }

    #[Route('/async/generate-report/{periodId}', name: 'app_async_generate_report')]
    public function generateReport(int $periodId, MessageBusInterface $messageBus): Response
    {
        $message = new GeneratePayrollReportMessage($periodId);
        $messageBus->dispatch($message);
        $this->addFlash('success', 'Asynchronous payroll generation started');

        return $this->redirectToRoute('app_async_tasks');
    }
}
