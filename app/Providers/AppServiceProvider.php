<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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
    public function boot()
    {
        if (config('app.debug')) {
            DB::listen(function ($query) {
                Log::debug("SQL: {$query->sql}", [
                    'bindings' => $query->bindings,
                    'time' => $query->time,
                ]);
            });
        }
    }
}
