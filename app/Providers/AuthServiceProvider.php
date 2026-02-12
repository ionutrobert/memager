<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        //
        //'Spatie\Permission\Models\Role' => 'App\Policies\RolePolicy',
        'Edwink\FilamentUserActivity\Models\UserActivity' => 'App\Policies\UserActivityPolicy',
        'Saade\FilamentLaravelLog\Pages\ViewLog' => 'App\Policies\UserPolicy',
        'Rappasoft\LaravelAuthenticationLog\Models\AuthenticationLog' => 'App\Policies\AuthenticationLogPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        //
    }
}
