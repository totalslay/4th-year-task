<?php

namespace App\Controller;

use App\Entity\SalaryCalculation;
use App\Form\SalaryCalculationType;
use App\Repository\SalaryCalculationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/salary/calculation')]
final class SalaryCalculationController extends AbstractController
{
    #[Route(name: 'app_salary_calculation_index', methods: ['GET'])]
    public function index(SalaryCalculationRepository $salaryCalculationRepository): Response
    {
        return $this->render('salary_calculation/index.html.twig', [
            'salary_calculations' => $salaryCalculationRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_salary_calculation_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $salaryCalculation = new SalaryCalculation();
        $form = $this->createForm(SalaryCalculationType::class, $salaryCalculation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($salaryCalculation);
            $entityManager->flush();

            return $this->redirectToRoute('app_salary_calculation_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('salary_calculation/new.html.twig', [
            'salary_calculation' => $salaryCalculation,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_salary_calculation_show', methods: ['GET'])]
    public function show(SalaryCalculation $salaryCalculation): Response
    {
        return $this->render('salary_calculation/show.html.twig', [
            'salary_calculation' => $salaryCalculation,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_salary_calculation_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, SalaryCalculation $salaryCalculation, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(SalaryCalculationType::class, $salaryCalculation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_salary_calculation_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('salary_calculation/edit.html.twig', [
            'salary_calculation' => $salaryCalculation,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_salary_calculation_delete', methods: ['POST'])]
    public function delete(Request $request, SalaryCalculation $salaryCalculation, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$salaryCalculation->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($salaryCalculation);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_salary_calculation_index', [], Response::HTTP_SEE_OTHER);
    }
}
