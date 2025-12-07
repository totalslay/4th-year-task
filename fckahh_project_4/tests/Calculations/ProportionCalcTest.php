<?php

namespace App\Tests\Calculations;

use App\Calculations\ProportionCalc;
use App\Repository\AccrualRepository;
use App\Repository\DeductionRepository;
use PHPUnit\Framework\TestCase;

class ProportionCalcTest extends TestCase
{
    public function testFullTimeEmployeeFullMonth()
    {
        $accrualRepo = $this->createMock(AccrualRepository::class);
        $deductionRepo = $this->createMock(DeductionRepository::class);

        $calc = new ProportionCalc($accrualRepo, $deductionRepo);
        $result = $calc->calcProportionalAccrual(300000, 'FULL_TIME', 22, 22);
        $this->assertEquals(300000, $result);
    }

    public function testFullTimeEmployeeHalfMonth()
    {
        $accrualRepo = $this->createMock(AccrualRepository::class);
        $deductionRepo = $this->createMock(DeductionRepository::class);

        $calc = new ProportionCalc($accrualRepo, $deductionRepo);
        $result = $calc->calcProportionalAccrual(300000, 'FULL_TIME', 11, 22);
        $this->assertEquals(150000, $result);
    }

    public function testPartTimeEmployee()
    {
        $accrualRepo = $this->createMock(AccrualRepository::class);
        $deductionRepo = $this->createMock(DeductionRepository::class);

        $calc = new ProportionCalc($accrualRepo, $deductionRepo);
        $result = $calc->calcProportionalAccrual(300000, 'PART_TIME', 22, 22);
        $this->assertEquals(150000, $result);
    }
}
