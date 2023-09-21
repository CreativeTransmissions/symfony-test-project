<?php

namespace App\Tests\Service;

use App\Service\VatCalculatorService;
use PHPUnit\Framework\TestCase;

class VatCalculatorServiceTest extends TestCase
{
    public function testCalcAmountExVat()
    {
        $service = new VatCalculatorService();
        $result = $service->calcAmountExVat(100, 20);
        $this->assertEquals(120, $result);
    }

    public function testCalcAmountIncVat()
    {
        $service = new VatCalculatorService();
        $result = $service->calcAmountIncVat(120, 20);
        $this->assertEquals(100, $result);
    }

    public function testCalcVatAmountExVat()
    {
        $service = new VatCalculatorService();
        $result = $service->calcVatAmountExVat(100, 0.2);
        $this->assertEquals(20, $result);
    }

    public function testCalcVatAmountIncVat()
    {
        $service = new VatCalculatorService();
        $result = $service->calcVatAmountIncVat(120, 0.2);
        $this->assertEquals(100, $result);
    }
}
