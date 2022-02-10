<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Interfaces\UserServiceInterface;
use App\Interfaces\AuthServiceInterface;
use App\Interfaces\WalletServiceInterface;
use App\Services\UserService;
use App\Services\AuthService;
use App\Services\WalletService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(UserServiceInterface::class, UserService::class);
        $this->app->bind(AuthServiceInterface::class, AuthService::class);
        $this->app->bind(WalletServiceInterface::class, WalletService::class);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        
    }
}
