<?php

namespace App\Service;

class VatCalculatorService
{
    public function calcAmountExVat($value, $vatAmount)
    {
        // value is exclusive of VAT
        return $value + $vatAmount;
    }

    public function calcAmountIncVat($value, $vatAmount)
    {   //value is inclusive of VAT
        return $value - $vatAmount;
    }    

    public function calcVatAmountExVat($value, $rate)
    {
        // value is exclusive of VAT
        return $value * $rate;
    } 
    
    public function calcVatAmountIncVat($value, $rate)
    {
        // value is inclusive of VAT
        return $value  / (1 + $rate);
    }     
}
