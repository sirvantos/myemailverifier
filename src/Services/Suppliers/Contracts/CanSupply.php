<?php

namespace Sirvantos\MyEmailVerifier\Services\Suppliers\Contracts;

interface CanSupply
{
    public function supply(array $array): object;
}
