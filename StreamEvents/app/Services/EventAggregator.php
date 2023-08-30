<?php
// app/Services/EventAggregator.php

namespace App\Services;

use App\Models\Follower;
use App\Models\Subscriber;
use App\Models\Donation;
use App\Models\MerchSale;
use Illuminate\Support\Collection;
use App\Modules\Payments\Currency;
use Illuminate\Support\Facades\Cache;
use App\Models\User;
use App\Models\Event;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\EventResource;
use App\Http\Resources\MerchSaleResource;
use App\Http\Resources\DonationResource;


class EventAggregator
{
    
    /**
     * @var User $user
     */
    protected $user;

    /**
     * @var int $ttl
     */
    protected $ttl;

    /**
     * Set user
     * @param User $user
     * @return StatsRepository
     */
    public function setUser(User $user)
    {
      $this->user = $user;

      return $this;
    }

    /**
     * Set ttl
     * @param int $ttl
     * @return StatsRepository
     */
    public function setTTL(int $ttl)
    {
        $this->ttl = $ttl;

        return $this;
    }
    
    public function aggregateEvents(int $last = 0, int $limit = 10)
    {
        
        $key = 'events:' . $this->user->id . ':' . $last . ':' . $limit;
        if(Cache::has($key)) {
            $result = Cache::get($key);
        } else {
            // Retrieve events from events table
            $events = Event::where('user_id', $this->user->id)
                ->whereRaw('created_at > FROM_UNIXTIME("'.$last.'")')
                ->orderBy('created_at', 'asc')
                ->take($limit)
                ->get();

            $groupedEvents = $events->groupBy('eventable_type');

            // Retrieve related event instances by ids
            foreach ($groupedEvents as $model => $instances) {
                $groupedEvents[$model] = $this->populate($model, $instances->pluck('eventable_id')->all());
            }

            // Add related event instance and prepare for json output
            $result = $events->map(function ($item) use ($groupedEvents) {
                $item->eventable = $groupedEvents[$item->eventable_type][$item->eventable_id];
                return new EventResource($item);
            })->all();

            if($result) {
                Cache::tags('events:' . $this->user)->put($key, $result, $this->ttl);
            }
        }

        return $result;
        
        
    }

        /**
     * 
     * @param int $days
     * @param array $subscriptionTierPrice
     * @return type
     */
    public function calculateRevenue(int $days, array $subscriptionTierPrice)
    {
        $key = 'stats:TR:' . $this->user->id;

        if (Cache::has($key)) {
            $total = Cache::get($key);
        } else {
            $total = Donation::where('user_id', $this->user->id)
                ->where('created_at', '>', now()->subDays($days)->endOfDay())
                ->sum('amount_usd');

            $total += MerchSale::where('user_id', $this->user->id)
                ->where('created_at', '>', now()->subDays($days)->endOfDay())
                ->sum(DB::raw('amount'));

            $total += Subscriber::where('user_id', $this->user->id)
                    ->where('created_at', '>', now()->subDays($days)->endOfDay())
                    ->where('tier_id', Subscriber::TIER1)
                    ->count() * $subscriptionTierPrice[Subscriber::TIER1];

            $total += Subscriber::where('user_id', $this->user->id)
                    ->where('created_at', '>', now()->subDays($days)->endOfDay())
                    ->where('tier_id', Subscriber::TIER2)
                    ->count() * $subscriptionTierPrice[Subscriber::TIER2];

            $total += Subscriber::where('user_id', $this->user->id)
                    ->where('created_at', '>', now()->subDays($days)->endOfDay())
                    ->where('tier_id', Subscriber::TIER3)
                    ->count() * $subscriptionTierPrice[Subscriber::TIER3];

            if ($total) {
                Cache::tags('events:' . $this->user->id)->put($key, $total, $this->ttl);
            }
        }

        return $total
            ? ['amount' => number_format($total, 2), 'currency' => Currency::USD]
            : null;
    }
    
    /**
     * 
     * @param int $days
     * @return type
     */
    public function calculateFollowersGained(int $days)
    {
        $key = 'stats:TF:' . $this->user->id;

        if(Cache::has($key)) {
            $total = Cache::get($key);
        } else {
            $total = Follower::where('user_id', $this->user->id)
                ->where('created_at', '>', now()->subDays($days)->endOfDay())
                ->count();

            if($total) {
                Cache::tags('events:' . $this->user->id)->put($key, $total, $this->ttl);
            }
        }

        return $total ?: null;
    }
    
    /**
     * 
     * @param int $days
     * @return type
     */
    public function getTopSellingItems(int $days)
    {
        $key = 'stats:BMS:' . $this->user->id;

        if(Cache::has($key)) {
            $result = Cache::get($key);
        } else {
            $result = MerchSale::where('user_id', $this->user->id)
                    ->selectRaw('item_name, SUM(amount) as total_sales')
                    ->where('created_at', '>', now()->subDays($days)->endOfDay())
            ->groupBy('item_name')
            ->orderByDesc(MerchSale::raw('SUM(amount)'))
            ->limit(3)
            ->get();
            if($result) {
                Cache::tags('events:' . $this->user->id)->put($key, $result->toArray(), $this->ttl);
            }
        }

        return count($result) ? $result->toArray() : null;
    }
    
    protected function populate(string $model, array $ids): array
    {
        $result = [];
        $resource = explode('\\', $model);
        $resource = 'App\Http\Resources\\' . array_pop($resource) . 'Resource';

        $model::whereIn('id', $ids)->get()
                ->each(function ($item) use (&$result, $resource) {
            $result[$item->id] = new $resource($item);
        });
        return $result;
    }

}
