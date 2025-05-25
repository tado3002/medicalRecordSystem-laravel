<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserLoginRequest;
use App\Http\Requests\UserRegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Support\Facades\Hash;



class AuthController extends Controller
{
    public function register(UserRegisterRequest $userRegisterRequest)
    {
        $data = $userRegisterRequest->validated();
        // throw conflict error if email exist
        $data['password'] = Hash::make($data['password']);
        $user = User::create($data);


        return $this->responseSuccess(
            'Registrasi user berhasil!',
            new UserResource($user),
            201
        );
    }

    public function login(UserLoginRequest $userLoginRequest)
    {
        $data = $userLoginRequest->validated();

        if (!auth()->attempt($data)) {
            $this->responseError('Email atau password salah!', 'WRONG_CREDENTIALS', 401);
        }
        $user = auth()->user();
        $ability = $user->role == 'ADMIN' ? ['user-resource'] : [];
        $token = $user->createToken('auth_token', $ability)->plainTextToken;

        return $this->responseSuccess('User berhasil login!', [
            'user' => new UserResource($user),
            'token' => ['accessToken' => $token]
        ]);
    }

    public function unauthenticated()
    {
        $this->responseError('Unauthenticated!', 'INVALID_CREDENTIAL', 401);
    }
}
