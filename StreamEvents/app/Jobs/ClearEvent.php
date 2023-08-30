<?php

namespace App\Jobs;

use App\Models\Donation;
use App\Models\Event;
use App\Models\Follower;
use App\Models\MerchSale;
use App\Models\Subscriber;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;

class ClearEvent implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var User $user
     */
    protected User $user;

    /**
     * Create a new job instance.
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Clear events for given user
        Donation::where('user_id', $this->user->id)->truncate();
        Follower::where('user_id', $this->user->id)->truncate();
        MerchSale::where('user_id', $this->user->id)->truncate();
        Subscriber::where('user_id', $this->user->id)->truncate();
        Event::where('user_id', $this->user->id)->truncate();

        Cache::tags('events:' . $this->user->id)->flush();
    }
}
