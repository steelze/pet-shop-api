<?php

namespace App\Providers;

use App\Auth\JWTGuard;
use App\Services\JWTService;
use Illuminate\Support\ServiceProvider;

class JWTAuthServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app['auth']->extend('jwt', function ($app, $name, array $config) {
            return new JWTGuard(
                $this->app['auth']->createUserProvider($config['provider']),
                $this->app['request'],
                new JWTService()
            );
        });
    }
}
