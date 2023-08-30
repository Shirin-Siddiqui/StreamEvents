<?php

namespace Database\Factories;

use App\Models\Subscriber;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;

/**
 * SubscriberFactory
 */
class SubscriberFactory extends Factory
{
    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        $date = $this->faker->dateTimeBetween('-3 months');

        return [
            'name' => $this->faker->name,
            'tier_id' => Arr::random([Subscriber::TIER1, Subscriber::TIER2, Subscriber::TIER3]),
            'user_id' => 1,
            'created_at' => $date,
            'updated_at' => $date
        ];
    }
}
