<?php

namespace App\Tests\Calculations;

use App\Calculations\TaxCalc;
use App\Entity\TaxRule;
use App\Repository\TaxRuleRepository;
use PHPUnit\Framework\TestCase;

class TaxCalcTest extends TestCase
{
    public function testTaxForZeroSalary()
    {
        $repo = $this->createMock(TaxRuleRepository::class);
        $calc = new TaxCalc($repo);
        
        $this->assertEquals(0, $calc->calcSalaryTax(0));
    }
    
    public function testBasicTaxCalculation()
    {
        $rule = new TaxRule(); //ставка 10% на всю сумму
        $rule->setMinAmount(0);
        $rule->setMaxAmount(null);
        $rule->setRate(0.10);
        $rule->setTaxType('INCOME_TAX');
        
        $repo = $this->createMock(TaxRuleRepository::class);
        $repo->method('findProgressiveTaxRules')->willReturn([$rule]);
        
        $calc = new TaxCalc($repo);
        
        $this->assertEquals(10000, $calc->calcSalaryTax(100000)); 
        $this->assertEquals(50000, $calc->calcSalaryTax(500000)); 
    }
    
    public function testTwoBracketTax()
    {
        $rule1 = new TaxRule();
        $rule1->setMinAmount(0);
        $rule1->setMaxAmount(100000);
        $rule1->setRate(0.10);
        $rule1->setTaxType('INCOME_TAX');
        
        $rule2 = new TaxRule();
        $rule2->setMinAmount(100001);
        $rule2->setMaxAmount(null);
        $rule2->setRate(0.20);
        $rule2->setTaxType('INCOME_TAX');

        $repo = $this->createMock(TaxRuleRepository::class);
        $repo->method('findProgressiveTaxRules')->willReturn([$rule1, $rule2]);
        $calc = new TaxCalc($repo);
        $this->assertEquals(20000, $calc->calcSalaryTax(150000));
    }
}