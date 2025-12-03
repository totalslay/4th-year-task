<?php

namespace App\Controller;

use App\Entity\TaxRule;
use App\Form\TaxRuleType;
use App\Repository\TaxRuleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/tax/rule')]
final class TaxRuleController extends AbstractController
{
    #[Route(name: 'app_tax_rule_index', methods: ['GET'])]
    public function index(TaxRuleRepository $taxRuleRepository): Response
    {
        return $this->render('tax_rule/index.html.twig', [
            'tax_rules' => $taxRuleRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_tax_rule_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $taxRule = new TaxRule();
        $form = $this->createForm(TaxRuleType::class, $taxRule);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($taxRule);
            $entityManager->flush();

            return $this->redirectToRoute('app_tax_rule_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('tax_rule/new.html.twig', [
            'tax_rule' => $taxRule,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_tax_rule_show', methods: ['GET'])]
    public function show(TaxRule $taxRule): Response
    {
        return $this->render('tax_rule/show.html.twig', [
            'tax_rule' => $taxRule,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_tax_rule_edit', methods: ['GET', 'PUT'])]
    public function edit(Request $request, TaxRule $taxRule, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(TaxRuleType::class, $taxRule, [
            'method' => 'PUT'
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_tax_rule_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('tax_rule/edit.html.twig', [
            'tax_rule' => $taxRule,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_tax_rule_delete', methods: ['DELETE'])]
    public function delete(Request $request, TaxRule $taxRule, EntityManagerInterface $entityManager): Response
    {
        $token = $request->request->get('_token') ?? $request->query->get('_token');
        if ($this->isCsrfTokenValid('delete'.$taxRule->getId(), $token)) {
            $entityManager->remove($taxRule);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_tax_rule_index', [], Response::HTTP_SEE_OTHER);
    }
}
