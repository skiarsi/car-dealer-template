<?php

namespace App\Providers;

use App\Models\Dealership;
use App\Models\ExternalLink;
use Illuminate\Support\Facades\Schema;
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
            $view->with(
                'externalLinks',
                Schema::hasTable('external_links')
                    ? ExternalLink::query()->visible()->get()
                    : collect(),
            );
        });
    }
}
