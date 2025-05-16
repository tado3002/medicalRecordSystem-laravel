<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserLoginRequest;
use App\Http\Requests\UserRegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;

class UsersController extends Controller
{
    public function register(UserRegisterRequest $userRegisterRequest): JsonResponse
    {
        $data = $userRegisterRequest->validated();
        // throw conflict error if email exist
        $this->throwConflictIfEmailFound($data['email']);
        $data['password'] = Hash::make($data['password']);
        $user = User::create($data);

        return (new UserResource($user))->response()->setStatusCode(201);
    }

    public function login(UserLoginRequest $userLoginRequest): UserResource
    {
        $data = $userLoginRequest->validated();
        $user = User::where('email', $data['email'])->first();
        if (!$user || !Hash::check($data['password'], $user->password)) {
            throw new HttpResponseException(response([
                'errors' => ['messages ' => ['email atau password salah!']]
            ], 404));
        }
        return new UserResource($user);
    }

    private function throwConflictIfEmailFound(string $email)
    {
        $user = User::where('email', $email)->first();
        if ($user) {
            throw new HttpResponseException(response([
                'errors' => ['messages ' => ['email sudah digunakan!']]
            ], 409));
        }
    }
}
