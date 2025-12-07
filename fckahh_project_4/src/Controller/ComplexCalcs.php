<?php

namespace App\Controller;

use App\Calculations\OvertimeCalc;
use App\Calculations\ProportionCalc;
use App\Calculations\TaxCalc;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Cache\CacheInterface;

class ComplexCalcs extends AbstractController
{
    #[Route('/complex/calc', name: 'complex_calc')]
    public function index(TaxCalc $taxCalc, OvertimeCalc $overtimeCalc, ProportionCalc $proportionCalc, CacheInterface $cache): Response
    {
        $periodId = 4;
        $tax = $taxCalc->getTax();
        $exampleTax = $taxCalc->calcSalaryTax(300000);
        $overtimes = $overtimeCalc->getOvertimeByPeriod($periodId);
        $highOvertimes = $overtimeCalc->getHighOvertimeEmployees($periodId);
        $proportionalDeductions = $proportionCalc->getDeductions('INCOME_TAX', $periodId);

        return $this->render('complex_calc.html.twig', [
            'tax' => $tax,
            'exampleTax' => $exampleTax,
            'overtimes' => $overtimes,
            'highOvertimes' => $highOvertimes,
            'proportionalDeductions' => $proportionalDeductions,
        ]);
    }
}
