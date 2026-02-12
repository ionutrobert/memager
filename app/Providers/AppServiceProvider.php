<?php

namespace App\Providers;

use Filament\Facades\Filament;
use Filament\Navigation\NavigationGroup;
use Illuminate\Support\Number;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;
use App\Rules\Cnp;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
        Number::useLocale( 'ro');

        // Register 'cnp' validation rule alias
        Validator::extend('cnp', function ($attribute, $value, $parameters, $validator) {
            $rule = new Cnp();
            return $rule->passes($attribute, $value);
        });

    }
}
