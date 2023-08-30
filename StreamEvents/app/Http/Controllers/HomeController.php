<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller; // Import the Controller class
use App\Models\Subscriber;
use Illuminate\Support\Facades\Auth;
use App\Jobs\GenerateEvent;


class HomeController extends Controller
{
    public function index(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }
        GenerateEvent::dispatch(Auth::user());
    
        return view('home');
    }
    
}
