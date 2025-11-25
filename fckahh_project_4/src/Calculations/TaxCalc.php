<?php

namespace App\Calculations;

use App\Repository\TaxRuleRepository;

class TaxCalc
{
    private $taxRuleRepository;

    public function __construct(TaxRuleRepository $taxRuleRepository)
    {
        $this->taxRuleRepository = $taxRuleRepository;
    }

    public function calcSalaryTax(float $salary): float
    {
        $taxRules = $this->taxRuleRepository->findProgressiveTaxRules();
        $tax = 0;
        $remainingSalary = $salary;

        foreach ($taxRules as $rule) {
            if ($remainingSalary <= 0) break;

            $bracketAmount = $rule->getMaxAmount() 
                ? min($remainingSalary, $rule->getMaxAmount() - $rule->getMinAmount())
                : $remainingSalary;

            if ($bracketAmount > 0) {
                $tax += $bracketAmount * $rule->getRate();
                $remainingSalary -= $bracketAmount;
            }
        }

        return $tax;
    }

    public function getTax(): array
    {
        return $this->taxRuleRepository->findProgressiveTaxRules();
    }
}