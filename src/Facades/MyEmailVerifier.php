<?php

namespace Sirvantos\Myemailverifier\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Sirvantos\Myemailverifier\Myemailverifier
 */
class Myemailverifier extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'myemailverifier';
    }
}
