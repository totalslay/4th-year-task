<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ComplexCalcsTest extends WebTestCase
{
    public function testComplexCalcPageLoads()
    {
        $client = static::createClient();
        $client->request('GET', '/complex/calc');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Complex calculations of the system');
    }

    public function testComplexCalcPageContainsSections()
    {
        $client = static::createClient();
        $client->request('GET', '/complex/calc');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('h2', 'Taxes on a progressive scale');
        $this->assertSelectorExists('h2', 'Employee overtime');
        $this->assertSelectorExists('h2', 'Proportional deductions');
        $this->assertSelectorExists('table');
    }

    public function testComplexCalcHasNavigationLinks()
    {
        $client = static::createClient();
        $client->request('GET', '/complex/calc');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('a[href*="/employee"]');
        $this->assertSelectorExists('a[href*="/payroll/period"]');
    }
}