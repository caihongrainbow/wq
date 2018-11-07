<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Schema;
use DB;
use Log;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);
        // \App\Models\UserAuth::observe(\App\Observers\UserAuthObserver::class);

        \Auth::provider('client-eloquent', function ($app, $config) {
            return new ClientEloquentUserProvider($this->app['hash'], $config['model']);
        });

        \Carbon\Carbon::setLocale('zh');

        // DB::listen(function ($query) {
            // $query->sql
            // $query->bindings
            // $query->time
            // Log::useFiles(storage_path().'/logs/test.log');
            // Log('info', $query->sql);
        // });
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
