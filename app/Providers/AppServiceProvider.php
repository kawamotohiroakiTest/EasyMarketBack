<?php

namespace App\Providers;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\ServiceProvider;
use Laravel\Cashier\Cashier;
use App\Services\easymarket\AuthService\AuthService;
use App\Services\easymarket\AuthService\AuthServiceInterface;
use App\Services\easymarket\ImageService\ImageService;
use App\Services\easymarket\ImageService\ImageServiceInterface;
use App\Services\easymarket\UserService\UserService;
use App\Services\easymarket\UserService\UserServiceInterface;
use App\Services\easymarket\ItemService\ItemService;
use App\Services\easymarket\ItemService\ItemServiceInterface;
//use ServiceeasymarketServiceProvider
use App\Providers\ServiceeasymarketServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Cashier::ignoreMigrations();
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        JsonResource::withoutWrapping();
    }
}
