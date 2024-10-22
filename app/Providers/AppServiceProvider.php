<?php

namespace App\Providers;

use App\Interfaces\BookRepositoryInterface;
use App\Repositories\DatabaseBookRepository;
use App\Repositories\DatabaseCustomerRepository;
use Illuminate\Support\ServiceProvider;
use App\Interfaces\CustomerRepositoryInterface;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(CustomerRepositoryInterface::class, DatabaseCustomerRepository::class);
        $this->app->bind(BookRepositoryInterface::class, DatabaseBookRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
