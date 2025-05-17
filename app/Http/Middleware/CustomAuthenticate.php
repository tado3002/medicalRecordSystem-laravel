<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Exceptions\HttpResponseException;

class CustomAuthenticate extends Middleware
{
    protected function unauthenticated($request, array $guards)
    {
        throw new HttpResponseException(response()->json([
            'errors' => [
                'messages' => ['Unauthorized!']
            ]
        ], 401));
    }
}
