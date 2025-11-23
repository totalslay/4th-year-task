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

    #[Route('/{id}/edit', name: 'app_payroll_period_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, PayrollPeriod $payrollPeriod, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(PayrollPeriodType::class, $payrollPeriod);
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

    #[Route('/{id}', name: 'app_payroll_period_delete', methods: ['POST'])]
    public function delete(Request $request, PayrollPeriod $payrollPeriod, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$payrollPeriod->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($payrollPeriod);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_payroll_period_index', [], Response::HTTP_SEE_OTHER);
    }
}
