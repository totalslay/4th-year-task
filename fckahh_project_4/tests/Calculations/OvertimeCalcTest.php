<?php

namespace App\Tests\Calculations;

use App\Calculations\OvertimeCalc;
use App\Repository\AccrualRepository;
use PHPUnit\Framework\TestCase;

class OvertimeCalcTest extends TestCase
{
    public function testGetOvertimeByPeriod()
    {
        $mockData = [
            ['fullName' => 'Abobus Kekeke', 'amount' => 15000, 'type' => 'OVERTIME'],
            ['fullName' => 'Oleg Mongol', 'amount' => 20000, 'type' => 'OVERTIME_NIGHT']
        ];
        
        $repository = $this->createMock(AccrualRepository::class);
        $repository->method('findOvertimeByPeriod')
            ->with(1)
            ->willReturn($mockData);
        
        $calc = new OvertimeCalc($repository);
        $result = $calc->getOvertimeByPeriod(1);
        $this->assertEquals($mockData, $result);
    }

    public function testGetHighOvertimeEmployees()
    {
        $mockData = [
            ['id' => 1, 'fullName' => 'Abobus Kekeke', 'totalOvertime' => 25000],
            ['id' => 2, 'fullName' => 'Oleg Mongol', 'totalOvertime' => 30000]
        ];
        $repository = $this->createMock(AccrualRepository::class);
        $repository->method('findEmployeesOvertime')
            ->with(1)
            ->willReturn($mockData);
        
        $calc = new OvertimeCalc($repository);
        $result = $calc->getHighOvertimeEmployees(1);
        $this->assertEquals($mockData, $result);
        $this->assertCount(2, $result);
        $this->assertEquals('Abobus Kekeke', $result[0]['fullName']);
        $this->assertEquals(25000, $result[0]['totalOvertime']);
    }

    public function testGetOvertimeByPeriodEmpty()
    {
        $repository = $this->createMock(AccrualRepository::class);
        $repository->method('findOvertimeByPeriod')
            ->with(999)
            ->willReturn([]);
        $calc = new OvertimeCalc($repository);
        $result = $calc->getOvertimeByPeriod(999);
        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }
}