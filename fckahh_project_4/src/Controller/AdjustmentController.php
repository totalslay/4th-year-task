<?php

namespace App\Controller;

use App\Entity\Adjustment;
use App\Form\AdjustmentType;
use App\Repository\AdjustmentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/adjustment')]
final class AdjustmentController extends AbstractController
{
    #[Route(name: 'app_adjustment_index', methods: ['GET'])]
    public function index(AdjustmentRepository $adjustmentRepository): Response
    {
        return $this->render('adjustment/index.html.twig', [
            'adjustments' => $adjustmentRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_adjustment_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $adjustment = new Adjustment();
        $form = $this->createForm(AdjustmentType::class, $adjustment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($adjustment);
            $entityManager->flush();

            return $this->redirectToRoute('app_adjustment_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('adjustment/new.html.twig', [
            'adjustment' => $adjustment,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_adjustment_show', methods: ['GET'])]
    public function show(Adjustment $adjustment): Response
    {
        return $this->render('adjustment/show.html.twig', [
            'adjustment' => $adjustment,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_adjustment_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Adjustment $adjustment, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(AdjustmentType::class, $adjustment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_adjustment_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('adjustment/edit.html.twig', [
            'adjustment' => $adjustment,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_adjustment_delete', methods: ['POST'])]
    public function delete(Request $request, Adjustment $adjustment, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$adjustment->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($adjustment);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_adjustment_index', [], Response::HTTP_SEE_OTHER);
    }
}
