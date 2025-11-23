<?php

namespace App\Controller;

use App\Entity\Accrual;
use App\Form\AccrualType;
use App\Repository\AccrualRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/accrual')]
final class AccrualController extends AbstractController
{
    #[Route(name: 'app_accrual_index', methods: ['GET'])]
    public function index(AccrualRepository $accrualRepository): Response
    {
        return $this->render('accrual/index.html.twig', [
            'accruals' => $accrualRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_accrual_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $accrual = new Accrual();
        $form = $this->createForm(AccrualType::class, $accrual);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($accrual);
            $entityManager->flush();

            return $this->redirectToRoute('app_accrual_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('accrual/new.html.twig', [
            'accrual' => $accrual,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_accrual_show', methods: ['GET'])]
    public function show(Accrual $accrual): Response
    {
        return $this->render('accrual/show.html.twig', [
            'accrual' => $accrual,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_accrual_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Accrual $accrual, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(AccrualType::class, $accrual);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_accrual_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('accrual/edit.html.twig', [
            'accrual' => $accrual,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_accrual_delete', methods: ['POST'])]
    public function delete(Request $request, Accrual $accrual, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$accrual->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($accrual);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_accrual_index', [], Response::HTTP_SEE_OTHER);
    }
}
