<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Laravel\Passport\Passport;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
         'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Passport::personalAccessClientId(config('passport.personal_access_client.id'));

        Passport::personalAccessClientSecret(config('passport.personal_access_client.secret'));

        Passport::tokensExpireIn(now()->addDays(10));

        Passport::refreshTokensExpireIn(now()->addDays(10));

        Passport::personalAccessTokensExpireIn(now()->addDays(10));

    }
}
