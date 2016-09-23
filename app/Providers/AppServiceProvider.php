<?php

namespace App\Providers;

use App\Group;
use App\Offer;
use App\User;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        view()->composer('admin', function ($view) {
            $view->with('globalGroups', ['' => 'Choose Group'] + Group::pluck('name', 'id')->all());
            $view->with('globalOffers', ['' => 'All Offer'] + Offer::pluck('name', 'id')->all());
            $view->with('globalUsers', User::pluck('username')->all());
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
