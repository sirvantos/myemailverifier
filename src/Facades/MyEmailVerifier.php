<?php

namespace Sirvantos\MyEmailVerifier\Facades;

use Illuminate\Support\Facades\Facade;

class MyEmailVerifier extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'myemailverifier';
    }
}
