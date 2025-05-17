<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use Illuminate\Http\Request;

class UsersController extends Controller
{
    public function profile(Request $request): UserResource
    {
        return new UserResource($request->user());
    }
}
