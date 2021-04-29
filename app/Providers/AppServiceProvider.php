<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */

    //https://stackoverflow.com/questions/26077458/laravel-migration-table-already-exists-but-i-want-to-add-new-not-the-older
    public function boot()
    {
        Schema::defaultStringLength(191);
        Paginator::useBootstrap();
    }
}
