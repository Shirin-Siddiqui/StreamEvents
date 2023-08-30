<?php

namespace Database\Factories;

use App\Modules\Payments\Currency;
use App\Modules\Payments\Converters\CurrencyConverter;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * DonationFactory
 */
class DonationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $date = $this->faker->dateTimeBetween('-3 months');
        $amount = $this->faker->randomFloat(2, 5, 100);
        $currency = collect(Currency::all())->values()->random();
        $converter = resolve(CurrencyConverter::class);
        return [
            'amount' => $amount,
            'amount_usd' => $converter->convert($amount, $currency, Currency::USD),
            'message' => $this->faker->text(150),
            'currency' => $currency,
            'user_id' => 1,
            'created_at' => $date,
            'updated_at' => $date
        ];
    }
}
