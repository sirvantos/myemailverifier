<?php

namespace Sirvantos\MyEmailVerifier;

use Sirvantos\MyEmailVerifier\Services\Suppliers\Contracts\CanSupply;
use Sirvantos\MyEmailVerifier\Services\Suppliers\Guzzle;
use Illuminate\Support\ServiceProvider;

class MyEmailVerifierServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/myemailverifier.php', 'myemailverifier');

        $this->app->bind(CanSupply::class, fn($app) => new Guzzle());

        $this->app->singleton('myemailverifier', function () {
            return new MyEmailVerifier();
        });

        $this->app->alias(MyEmailVerifier::class, 'myemailverifier');
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['myemailverifier'];
    }
}
