<?php

namespace App\Providers;

use App\Repositories\ConversionRepository;
use App\Repositories\Interfaces\ConversionRepositoryInterface;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
      $this->app->bind(
        ConversionRepositoryInterface::class,
        ConversionRepository::class
      );
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
