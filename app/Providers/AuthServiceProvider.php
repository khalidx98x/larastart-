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
        // 'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {

        $this->registerPolicies();

        Gate::define('isAdmin', function ($user) {

            return $user->type === 'admin'; //return true if the current user type is equal to admin
        });

        Gate::define('isAuthor', function ($user) {

            return $user->type === 'author'; //return true if the current user type is equal to author
        });

        Gate::define('isUser', function ($user) {

            return $user->type === 'user'; //return true if the current user type is equal to user
        });

        Passport::routes();


        //
    }
}
