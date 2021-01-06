<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        'http://apirest-laravel.test/register',
        'http://apirest-laravel.test/courses',
        'http://apirest-laravel.test/courses/*'
    ];
}
