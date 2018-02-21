<?php

namespace Gabrielmoura\Laravel-AuthSSL\Facades;

use Illuminate\Support\Facades\Facade;

class AuthSSL extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'authssl';
    }

}