<?php

namespace App\Providers;

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
        // \App\Models\User::observe(\App\Observers\UserObserver::class);

        \Auth::provider('custom-eloquent', function ($app, $config) {
            return new CustomEloquentUserProvider($this->app['hash'], $config['model']);
        });

        \Carbon\Carbon::setLocale('zh');
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
