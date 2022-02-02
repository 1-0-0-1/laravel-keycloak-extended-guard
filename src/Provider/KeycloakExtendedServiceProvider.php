<?php

declare(strict_types=1);

namespace KeycloakExtendedGuard\Provider;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;
use KeycloakExtendedGuard\Guard\KeycloakExtendedGuard;
use function app;

class KeycloakExtendedServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/keycloak.php' => app()->configPath('keycloak.php'),
        ], 'config');

        $this->mergeConfigFrom(__DIR__ . '/../config/keycloak.php', 'keycloak');
    }

    public function register()
    {
        Auth::extend('keycloak', function ($app, $name, array $config) {
            return new KeycloakExtendedGuard(Auth::createUserProvider($config['provider']), $app->request);
        });
    }
}
