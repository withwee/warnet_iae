<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\View;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot()
{
    View::composer('*', function ($view) {
        try {
            $token = JWTAuth::getToken() ?? session('jwt_token');
            $user = $token ? JWTAuth::setToken($token)->authenticate() : null;
        } catch (\Exception $e) {
            $user = null;
        }

        $message = session('message') ?? '';

        $view->with('user', $user)->with('message', $message);
    });
}

}
