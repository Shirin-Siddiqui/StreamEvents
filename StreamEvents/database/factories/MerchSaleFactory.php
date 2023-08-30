<?php

namespace Database\Factories;

use App\Modules\Payments\Converters\CurrencyConverter;
use App\Modules\Payments\Currency;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * MerchSaleFactory
 */
class MerchSaleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $date = $this->faker->dateTimeBetween('-3 months');
        $price = $this->faker->randomFloat(2, 5, 100);
        $currency = collect(Currency::all())->values()->random();
        $converter = resolve(CurrencyConverter::class);

        return [
            'item_name' => $this->faker->word,
            'amount' => $this->faker->randomNumber(1),
            'price' => $price,
            'price_usd' => $converter->convert($price, $currency, Currency::USD),
            'currency' => $currency,
            'user_id' => 1,
            'created_at' => $date,
            'updated_at' => $date
        ];
    }
}
