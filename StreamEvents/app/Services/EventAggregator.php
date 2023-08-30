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
