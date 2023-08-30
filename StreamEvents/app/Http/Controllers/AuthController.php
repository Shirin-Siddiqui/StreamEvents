<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Jobs\GenerateEvent;
use App\Jobs\ClearEvent;
use Laravel\Socialite\Facades\Socialite; // Import the Socialite facade
use Illuminate\Support\Facades\Auth; // Import the Auth facade
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthController extends Controller {

    /**
     * 
     * @return type
     */
    public function login() {
       
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        return view('welcome');
    }
    
    /**
     * 
     * @param type $provider
     * @return type
     */
    public function redirect($provider ) {
        return (Socialite::driver($provider)->redirect());
    }
    
    /**
     * 
     * @param type $provider
     * @return type
     */
    public function callback($provider ) {
        $socialUser = Socialite::driver($provider)->stateless()->user();
        $user = User::where('email', $socialUser->email)->first();
        if (empty($user)) {
            $user = User::create([
                        'name' => $socialUser->name,
                        'email' => $socialUser->email,
                        'provider_user_id' => $socialUser->id,
                        'provider_id' => $provider,
                        'password' => Hash::make(Str::random(20))
            ]);
        }
        
        Auth::login($user);
        //dd(Auth::user());
        
        GenerateEvent::dispatch($user);
        return redirect()->route('dashboard');
    }

    public function logout() {
        ClearEvent::dispatch(Auth::user());
        Auth::guard('web')->logout();
        return redirect()->route('login');
    }

}
