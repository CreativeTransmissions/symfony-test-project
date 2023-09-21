<?php

namespace App\Service;

class VatCalculatorService
{
    public function calcAmountExVat($a, $b)
    {
        return $a + $b;
    }

    public function calcAmountIncVat($a, $b)
    {
        return $a + $b;
    }    
}
