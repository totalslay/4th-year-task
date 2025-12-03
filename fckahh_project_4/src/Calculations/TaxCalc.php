<?php

namespace App\Calculations;

use App\Repository\TaxRuleRepository;

class TaxCalc
{
    private TaxRuleRepository $taxRuleRepository;

    public function __construct(TaxRuleRepository $taxRuleRepository)
    {
        $this->taxRuleRepository = $taxRuleRepository;
    }

    public function calcSalaryTax(float $salary): float
{
    $taxRules = $this->taxRuleRepository->findProgressiveTaxRules();
    
    if (empty($taxRules)) {
        return 0.0;
    }
    
    $tax = 0.0;
    // сортировка по мин
    usort($taxRules, function($a, $b) {
        return ($a->getMinAmount() ?? 0) <=> ($b->getMinAmount() ?? 0);
    });

    foreach ($taxRules as $rule) {
        $min = $rule->getMinAmount() ?? 0;
        $max = $rule->getMaxAmount();
        $rate = $rule->getRate() ?? 0;
        if ($salary <= $min) {
            continue;
        }
        $amountInThisBracket = $salary - $min;
        // если есть верхний предел
        if ($max !== null && $max > $min) {
            $amountInThisBracket = min($amountInThisBracket, $max - $min);
        }
        
        if ($amountInThisBracket > 0) {
            $tax += $amountInThisBracket * $rate;
        }
    }
        return $tax;
    }
//тут короче отличие мизерное, нормально, а страдают как всегда работяги...
    public function getTax(): array
    {
        return $this->taxRuleRepository->findProgressiveTaxRules();
    }
}