<?php

namespace App\Providers;

use App\Models\BusinessSetting;
use Illuminate\Auth\Middleware\RedirectIfAuthenticated;
use Illuminate\Pagination\Paginator;
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
        RedirectIfAuthenticated::redirectUsing(fn () => route('dashboard'));

        Paginator::useBootstrapFive();

        View::composer('layouts.app', function ($view) {
            $view->with('business', BusinessSetting::current());
        });
    }
}
