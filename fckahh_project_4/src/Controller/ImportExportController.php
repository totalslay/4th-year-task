<?php

namespace App\Controller;

use App\Entity\PayrollPeriod;
use App\Repository\PayrollPeriodRepository;
use App\Service\TaxReportExporter;
use App\Service\TimeSheetImporter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/import-export')]
final class ImportExportController extends AbstractController
{
    #[Route('/', name: 'app_import_export_index')]
    public function index(PayrollPeriodRepository $periodRepository): Response
    {
        $periods = $periodRepository->findAll();

        return $this->render('import_export/index.html.twig', [
            'periods' => $periods,
        ]);
    }

    #[Route('/export/tax-report/{id}', name: 'app_export_tax_report')]
    public function exportTaxReport(
        PayrollPeriod $period,
        TaxReportExporter $exporter,
    ): Response {
        $csvContent = $exporter->exportTaxReport($period);

        $filename = \sprintf(
            'tax_report_%s_%s.csv',
            $period->getId(),
            date('Y-m-d')
        );

        $response = new Response($csvContent);
        $response->headers->set('Content-Type', 'text/csv');
        $response->headers->set('Content-Disposition',
            $response->headers->makeDisposition(
                ResponseHeaderBag::DISPOSITION_ATTACHMENT,
                $filename
            )
        );

        return $response;
    }

    #[Route('/import/timesheet/{id}', name: 'app_import_timesheet', methods: ['GET', 'POST'])]
    public function importTimeSheet(
        Request $request,
        PayrollPeriod $period,
        TimeSheetImporter $importer,
    ): Response {
        $result = null;

        if ($request->isMethod('POST')) {
            $file = $request->files->get('timesheet_file');

            if ($file && $file->isValid()) {
                $content = file_get_contents($file->getPathname());
                $result = $importer->importTimeSheet($content, $period);

                if (empty($result['errors'])) {
                    $this->addFlash('success',
                        \sprintf('Successfully imported %d records', $result['success'])
                    );
                } else {
                    $this->addFlash('warning',
                        \sprintf('Imported %d records with %d errors',
                            $result['success'],
                            \count($result['errors'])
                        )
                    );
                }
            } else {
                $this->addFlash('error', 'Please upload a valid CSV file');
            }
        }

        return $this->render('import_export/import_timesheet.html.twig', [
            'period' => $period,
            'result' => $result,
        ]);
    }

    #[Route('/download/template', name: 'app_download_template')]
    public function downloadTemplate(): Response
    {
        $template = "TIN;Worked Hours;Overtime Hours\n";
        $template .= "123456789012;160;10\n";
        $template .= "234567890123;150;5\n";
        $template .= "345678901234;170;15\n";

        $response = new Response($template);
        $response->headers->set('Content-Type', 'text/csv');
        $response->headers->set('Content-Disposition',
            $response->headers->makeDisposition(
                ResponseHeaderBag::DISPOSITION_ATTACHMENT,
                'timesheet_template.csv'
            )
        );

        return $response;
    }
}
