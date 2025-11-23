<?php

namespace App\Controller;

use App\Entity\Deduction;
use App\Form\DeductionType;
use App\Repository\DeductionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/deduction')]
final class DeductionController extends AbstractController
{
    #[Route(name: 'app_deduction_index', methods: ['GET'])]
    public function index(DeductionRepository $deductionRepository): Response
    {
        return $this->render('deduction/index.html.twig', [
            'deductions' => $deductionRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_deduction_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $deduction = new Deduction();
        $form = $this->createForm(DeductionType::class, $deduction);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($deduction);
            $entityManager->flush();

            return $this->redirectToRoute('app_deduction_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('deduction/new.html.twig', [
            'deduction' => $deduction,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_deduction_show', methods: ['GET'])]
    public function show(Deduction $deduction): Response
    {
        return $this->render('deduction/show.html.twig', [
            'deduction' => $deduction,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_deduction_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Deduction $deduction, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(DeductionType::class, $deduction);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_deduction_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('deduction/edit.html.twig', [
            'deduction' => $deduction,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_deduction_delete', methods: ['POST'])]
    public function delete(Request $request, Deduction $deduction, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$deduction->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($deduction);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_deduction_index', [], Response::HTTP_SEE_OTHER);
    }
}
