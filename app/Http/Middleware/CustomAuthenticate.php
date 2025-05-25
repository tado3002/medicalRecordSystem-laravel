<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Exceptions\HttpResponseException;
use Laravel\Sanctum\PersonalAccessToken;

class CustomAuthenticate extends Middleware
{
    protected function unauthenticated($request, array $guards)
    {
        $token = $request->bearerToken() ?
            PersonalAccessToken::findToken($request->bearerToken()) : false;
        if ($token) {
            parent::authenticate($request, $guards);
        }
        throw new HttpResponseException(response()->json([
            'errors' => [
                'messages' => ['Token is invalid!']
            ]
        ], 401));
    }
}
