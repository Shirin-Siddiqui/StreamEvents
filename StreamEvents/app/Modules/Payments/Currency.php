<?php

namespace App\Modules\Payments;

use Illuminate\Support\Facades\Cache;
use ReflectionClass;

class Currency
{
    const USD = 'USD';
    const CAD = 'CAD';
    const EUR = 'EUR';


    /**
     * Returns all available currencies
     * @return mixed
     */
    public static function all()
    {
        return Cache::remember('SE:cur', 5 * 60, function () {
            return (new ReflectionClass(__CLASS__))->getConstants();
        });
    }
}
