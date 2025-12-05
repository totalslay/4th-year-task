<?php

namespace App\Controller;

use App\Entity\PayrollPeriod;
use App\Form\PayrollPeriodType;
use App\Repository\PayrollPeriodRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/payroll/period')]
final class PayrollPeriodController extends AbstractController
{
    #[Route(name: 'app_payroll_period_index', methods: ['GET'])]
    public function index(PayrollPeriodRepository $payrollPeriodRepository): Response
    {
        return $this->render('payroll_period/index.html.twig', [
            'payroll_periods' => $payrollPeriodRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_payroll_period_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $payrollPeriod = new PayrollPeriod();
        $form = $this->createForm(PayrollPeriodType::class, $payrollPeriod);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($payrollPeriod);
            $entityManager->flush();

            return $this->redirectToRoute('app_payroll_period_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('payroll_period/new.html.twig', [
            'payroll_period' => $payrollPeriod,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_payroll_period_show', methods: ['GET'])]
    public function show(PayrollPeriod $payrollPeriod): Response
    {
        return $this->render('payroll_period/show.html.twig', [
            'payroll_period' => $payrollPeriod,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_payroll_period_edit', methods: ['GET', 'PUT'])]
    public function edit(Request $request, PayrollPeriod $payrollPeriod, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(PayrollPeriodType::class, $payrollPeriod, [
            'method' => 'PUT'
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_payroll_period_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('payroll_period/edit.html.twig', [
            'payroll_period' => $payrollPeriod,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_payroll_period_delete', methods: ['DELETE'])]
    public function delete(Request $request, PayrollPeriod $payrollPeriod, EntityManagerInterface $entityManager): Response
    {
        $token = $request->request->get('_token') ?? $request->query->get('_token');
        if ($this->isCsrfTokenValid('delete'.$payrollPeriod->getId(), $token)) {
            $entityManager->remove($payrollPeriod);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_payroll_period_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/approve', name: 'app_payroll_period_approve', methods: ['POST'])]
    public function approve(Request $request, PayrollPeriod $payrollPeriod, EntityManagerInterface $entityManager):Response
    {
        if ($this->isCsrfTokenValid('approve'.$payrollPeriod->getId(), $request->request->get('_token'))) {
            try {
                $payrollPeriod->approve();
                $entityManager->flush();
                $this->addFlash('success', 'Payroll period approved successfully');
            } catch (\RuntimeException $e) {
                $this->addFlash('error', $e->getMessage());
            }
        }
        return $this->redirectToRoute('app_payroll_period_show', ['id' => $payrollPeriod->getId()]);
    }

    #[Route('/{id}/process', name: 'app_payroll_period_process', methods: ['POST'])]
    public function process(Request $request, PayrollPeriod $payrollPeriod, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('process'.$payrollPeriod->getId(), $request->request->get('_token'))) {
            try {
                $payrollPeriod->process();
                $entityManager->flush();
                $this->addFlash('success', 'Payroll period processed successfully');
            } catch (\RuntimeException $e) {
                $this->addFlash('error', $e->getMessage());
            }
        }

        return $this->redirectToRoute('app_payroll_period_show', ['id' => $payrollPeriod->getId()]);
    }

    #[Route('/{id}/cancel', name: 'app_payroll_period_cancel', methods: ['POST'])]
    public function cancel(Request $request, PayrollPeriod $payrollPeriod, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('cancel'.$payrollPeriod->getId(), $request->request->get('_token'))) {
            try {
                $payrollPeriod->cancel();
                $entityManager->flush();
                $this->addFlash('success', 'Payroll period cancelled successfully');
            } catch (\RuntimeException $e) {
                $this->addFlash('error', $e->getMessage());
            }
        }

        return $this->redirectToRoute('app_payroll_period_show', ['id' => $payrollPeriod->getId()]);
    }

    #[Route('/{id}/reopen', name: 'app_payroll_period_reopen', methods: ['POST'])]
    public function reopen(Request $request, PayrollPeriod $payrollPeriod, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('reopen'.$payrollPeriod->getId(), $request->request->get('_token'))) {
            try {
                $payrollPeriod->reopen();
                $entityManager->flush();
                $this->addFlash('success', 'Payroll period reopened successfully');
            } catch (\RuntimeException $e) {
                $this->addFlash('error', $e->getMessage());
            }
        }

        return $this->redirectToRoute('app_payroll_period_show', ['id' => $payrollPeriod->getId()]);
    }
}
