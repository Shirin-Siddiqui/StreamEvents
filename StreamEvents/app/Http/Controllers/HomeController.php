<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller; // Import the Controller class
use App\Models\Subscriber;
use Illuminate\Support\Facades\Auth;
use App\Jobs\GenerateEvent;
use App\Services\EventAggregator; // Import the EventAggregator class

class HomeController extends Controller
{
    protected $eventAggregator;

    public function __construct(EventAggregator $eventAggregator)
    {
        $this->eventAggregator = $eventAggregator;
    }

    public function index(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }
        GenerateEvent::dispatch(Auth::user());
        $days = 30;
        $subscriptionTierPrice = [
            Subscriber::TIER1 => 5,
            Subscriber::TIER2 => 10,
            Subscriber::TIER3 => 15,
        ];
        
        ## use TTL to maintain cache
        $this->eventAggregator->setUser($request->user())
            ->setTTL(5 * 60);
        
        $totalRevenue = $this->eventAggregator->calculateRevenue($days, $subscriptionTierPrice);
        $followersGained = $this->eventAggregator->calculateFollowersGained($days);
        $topItems = $this->eventAggregator->getTopSellingItems($days);
        
        return view('home', compact( 'totalRevenue', 'followersGained', 'topItems'));
    }
}
