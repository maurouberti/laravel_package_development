<?php

namespace Modules\Location\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

class LocationServiceProvider extends ServiceProvider {
    public function boot() {
        Route::middleware(['web'])->group(__DIR__.'/../Routes/web.php');
    }
    
    public function register() {
        //
    }
}