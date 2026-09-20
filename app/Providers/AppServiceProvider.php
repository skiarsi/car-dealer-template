<?php

namespace App\Providers;

use App\Models\Dealership;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        View::composer(['layouts.app', 'layouts.admin', 'layouts::app', 'layouts::admin'], function ($view): void {
            $view->with('dealership', Dealership::current());
        });
    }
}
