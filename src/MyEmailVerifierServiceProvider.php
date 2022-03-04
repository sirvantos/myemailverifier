<?php

namespace Sirvantos\Myemailverifier;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Sirvantos\Myemailverifier\Commands\MyemailverifierCommand;

class MyemailverifierServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('myemailverifier')
            ->hasConfigFile()
            ->hasViews()
            ->hasMigration('create_myemailverifier_table')
            ->hasCommand(MyemailverifierCommand::class);
    }
}
