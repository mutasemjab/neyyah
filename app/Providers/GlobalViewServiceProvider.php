<?php

namespace App\Providers;

use App\Models\Setting;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;

class GlobalViewServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

   public function boot()
    {
        // // Get the settings
        // $setting = Setting::first();

        // // Get current locale
        // $locale = app()->getLocale();

        // // Share with all views
        // View::share([
        //     'setting' => $setting,
        //     'locale' => $locale,
        // ]);
    }
}
