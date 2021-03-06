<?php

namespace App\Providers;

use App\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use App\Policies\UserPolicy;
use Illuminate\Support\Str;

class AuthServiceProvider extends ServiceProvider
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
     * Boot the authentication services for the application.
     *
     * @return void
     */
    public function boot()
    {
        // Here you may define how you wish users to be authenticated for your Lumen
        // application. The callback which receives the incoming request instance
        // should return either a User instance or null. You're free to obtain
        // the User instance via an API token or any other method necessary.

        $this->app['auth']->viaRequest('api', function ($request) {
            $api_token = $request->input('api_token');
            if(!$api_token)
            {
                $header = $request->header('Authorization', '');
                if (Str::startsWith($header, 'Bearer ')) {
                    $api_token = Str::substr($header, 7);
                }
            }
            if ($api_token) {
                return User::where('api_token', $api_token)->first();
            }
        });


        # Gate::policy('App\User', 'App\Policies\UserPolicy');
        foreach (['create', 'update', 'destroy'] as $action) {
            Gate::define($action, "App\Policies\UserPolicy@$action");
        }

    }
}
