<?php

namespace App\Service;

use NumberToWords\NumberToWords;

class NumberToWordsService
{
    private $numberToWords;

    public function __construct()
    {
        $this->numberToWords = new NumberToWords();
    }

    public function convertToFrench(int $number): string
    {
        return ucfirst($this->numberToWords->getNumberTransformer('fr')->toWords($number));
    }
}