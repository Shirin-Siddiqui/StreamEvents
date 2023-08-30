<?php

namespace Database\Seeders;

use App\Models\Donation;
use App\Models\Follower;
use App\Models\MerchSale;
use App\Models\Subscriber;
use Illuminate\Database\Seeder;

class EventSeeder extends Seeder
{

    /**
     * Run the database seeds.
     */
    public function run(int $userId , int $count = 400): void
    {
        
        ## Generate new events for  user
        Donation::factory()->count($count)
            ->afterCreating(function ($model) {
                $model->event()->create([
                    'user_id' => $model->user_id,
                    'created_at' => $model->created_at,
                    'updated_at' => $model->updated_at,
                ]);
            })
            ->create(['user_id' => $userId]);

        Follower::factory()->count($count)
            ->afterCreating(function ($model) {
                $model->event()->create([
                    'user_id' => $model->user_id,
                    'created_at' => $model->created_at,
                    'updated_at' => $model->updated_at,
                ]);
            })
            ->create(['user_id' => $userId]);

        MerchSale::factory()->count($count)
            ->afterCreating(function ($model) {
                $model->event()->create([
                    'user_id' => $model->user_id,
                    'created_at' => $model->created_at,
                    'updated_at' => $model->updated_at,
                ]);
            })
            ->create(['user_id' => $userId]);

        Subscriber::factory()->count($count)
            ->afterCreating(function ($model) {
                $model->event()->create([
                    'user_id' => $model->user_id,
                    'created_at' => $model->created_at,
                    'updated_at' => $model->updated_at,
                ]);
            })
            ->create(['user_id' => $userId]);
    }
}
