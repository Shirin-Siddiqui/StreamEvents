<?php

namespace App\Modules\Payments\Converters;

interface CurrencyConverter
{
    /**
     * Converts amount from one currency to another
     * @param float $amount
     * @param string $currencyFrom
     * @param string $currencyTo
     * @return mixed
     */
    public function convert(float $amount, string $currencyFrom, string $currencyTo): float;
}
