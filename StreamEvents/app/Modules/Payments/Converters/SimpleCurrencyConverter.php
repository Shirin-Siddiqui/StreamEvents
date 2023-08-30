<?php

namespace App\Modules\Payments\Converters;

class SimpleCurrencyConverter implements CurrencyConverter
{
    /**
     * @var array $rates
     */
    protected array $rates;

    /**
     * @param array $rates
     */
    public function __construct(array $rates)
    {
        $this->rates = $rates;
    }

    /**
     * @inheritDoc
     */
    public function convert(float $amount, string $currencyFrom, string $currencyTo): float
    {
        return $amount * $this->rates[$currencyFrom][$currencyTo];
    }
}
