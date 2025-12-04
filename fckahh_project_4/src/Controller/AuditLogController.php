<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class AuditLogController extends AbstractController
{
    #[Route('/audit/logs', name: 'app_audit_logs')]
    public function index(): Response
    {
        $logFile = $this->getParameter('kernel.logs_dir') . '/audit.log';
        $logs = [];
        if (file_exists($logFile)) {
            $lines = file($logFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            $logs = array_slice(array_reverse($lines), 0, 50);
            $parsedLogs = [];
            foreach ($logs as $log) {
                if (strpos($log, 'Manual adjustment') !== false) {
                    $jsonStart = strpos($log, '{');
                    if ($jsonStart !== false) {
                        $jsonStr = substr($log, $jsonStart);
                        $data = json_decode($jsonStr, true);
                        if ($data) {
                            $parsedLogs[] = $data;
                        }
                    }
                }
            }
            $logs = $parsedLogs;
        }

        return $this->render('audit_log/index.html.twig', [
            'logs' => $logs,
            'log_file' => $logFile,
            'has_logs' => file_exists($logFile) && filesize($logFile) > 0
        ]);
    }

    #[Route('/audit/logs/clear', name: 'app_audit_logs_clear', methods: ['POST'])]
    public function clear():Response
    {
        $logFile = $this->getParameter('kernel.logs_dir') . '/audit.log';

        if (file_exists($logFile)) {
            file_put_contents($logFile, '');
            $this->addFlash('success', 'Logs cleared');
        }
        return $this->redirectToRoute('app_audit_logs');
    }
}
