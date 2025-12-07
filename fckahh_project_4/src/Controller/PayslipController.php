<?php

namespace App\Controller;

use App\Entity\Payslip;
use App\Form\PayslipType;
use App\Repository\PayslipRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/payslip')]
final class PayslipController extends AbstractController
{
    #[Route(name: 'app_payslip_index', methods: ['GET'])]
    public function index(PayslipRepository $payslipRepository): Response
    {
        return $this->render('payslip/index.html.twig', [
            'payslips' => $payslipRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_payslip_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $payslip = new Payslip();
        $form = $this->createForm(PayslipType::class, $payslip);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($payslip);
            $entityManager->flush();

            return $this->redirectToRoute('app_payslip_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('payslip/new.html.twig', [
            'payslip' => $payslip,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_payslip_show', methods: ['GET'])]
    public function show(Payslip $payslip): Response
    {
        return $this->render('payslip/show.html.twig', [
            'payslip' => $payslip,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_payslip_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Payslip $payslip, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(PayslipType::class, $payslip);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_payslip_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('payslip/edit.html.twig', [
            'payslip' => $payslip,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_payslip_delete', methods: ['POST'])]
    public function delete(Request $request, Payslip $payslip, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$payslip->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($payslip);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_payslip_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/download/{id}', name: 'app_payslip_download', methods: ['GET'])]
    public function download(Payslip $payslip): Response
    {
        $pdfDirectory = $this->getParameter('pdf_directory');
        $pdfPath = $pdfDirectory.'/'.$payslip->getPdfFilename();

        if (!file_exists($pdfPath)) {
            throw $this->createNotFoundException('PDF file not found: '.$pdfPath);
        }

        return $this->file($pdfPath);
    }
}
