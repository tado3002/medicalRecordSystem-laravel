<?php

use App\Models\User;
use Database\Seeders\UserSeeder;
use Laravel\Sanctum\PersonalAccessToken;

use function Pest\Laravel\post;
use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertNull;

describe('register user', function () {
    test('success', function () {
        $data = [
            'name' => 'Muhammad Murtadlo',
            'email' => 'muh.murtadlo23@gmail.com',
            'password' => 'apikwid123',
            'role' => 'NURSE',
            'phone' => '08214780323'
        ];
        $expected = responseSuccess('Registrasi user berhasil!', toUserResponse($data));
        post('/api/auth/register', $data)
            ->assertCreated()
            ->assertJson($expected);
    });

    test('failed cause invalid data request', function () {
        $data = [
            'name' => '',
            'email' => 'blablabla',
            'password' => 'wudes',
            'role' => 'bawak',
            'phone' => '088'

        ];
        post('/api/auth/register', $data)
            ->assertUnprocessable()
            ->assertJson(responseError('User request tidak valid!', [
                'name' => ["The name field is required."],
                'email' => ["The email field must be a valid email address."],
                'password' => ["The password field must be at least 8 characters."],
                'role' => ["The selected role is invalid."],
                'phone' => ["The phone field must be at least 10 characters."]
            ], 'REQUEST_INVALID'));
    });

    test('failed cause email is exist', function () {
        $this->seed([UserSeeder::class]);
        $data = [
            'name' => 'Muhammad Murtadlo',
            'email' => 'test@gmail.com',
            'password' => 'apikwid123',
            'role' => 'NURSE',
            'phone' => '08214780323'
        ];
        post('/api/auth/register', $data)
            ->assertUnprocessable()
            ->assertJson(responseError('User request tidak valid!', [
                'email' => ["The email has already been taken."],
            ], 'REQUEST_INVALID'));
    });
});

describe('user login', function () {
    test('success', function () {
        $this->seed([UserSeeder::class]);
        $user = getUser();
        post('/api/auth/login', [
            'email' => 'test@gmail.com',
            'password' => 'testtesttest',
            'device_name' => 'vivo'
        ])
            ->assertOk()
            ->assertSee('accessToken');
        $token = getToken();
        assertEquals($user['id'], $token->tokenable_id);
    });
    test('failed cause email is wrong', function () {
        $this->seed([UserSeeder::class]);

        post('/api/auth/login', [
            'email' => 'test1@gmail.com',
            'password' => 'testtesttest'
        ])->assertUnauthorized()->assertJson(
            responseError('Email atau password salah!', null, 'WRONG_CREDENTIALS')
        );
        $token = getToken();
        assertNull($token);
    });
    test('failed cause password is wrong', function () {
        $this->seed([UserSeeder::class]);

        post('/api/auth/login', [
            'email' => 'test@gmail.com',
            'password' => 'salahslaah'
        ])->assertUnauthorized()->assertJson(
            responseError('Email atau password salah!', null, 'WRONG_CREDENTIALS')
        );
        $token = getToken();
        assertNull($token);
    });
    test('failed cause password request is empty', function () {
        $this->seed([UserSeeder::class]);

        post('/api/auth/login', [
            'email' => 'test@gmail.com',
            'password' =>  ''
        ])
            ->assertUnprocessable()
            ->assertJson(
                responseError('User request tidak valid!', [
                    'password' => ["The password field is required."]
                ], 'REQUEST_INVALID')
            );
        $token = getToken();
        assertNull($token);
    });
    test('failed cause email request is invalid', function () {
        $this->seed([UserSeeder::class]);

        post('/api/auth/login', [
            'email' => 'test',
            'password' =>  'testtesttest'
        ])
            ->assertUnprocessable()
            ->assertJson(
                responseError('User request tidak valid!', [
                    'email' => ["The email field must be a valid email address."]
                ], 'REQUEST_INVALID')
            );

        $token = getToken();
        assertNull($token);
    });
});



function toRegisterResponse($data)
{
    return [
        'user' => toUserResponse($data),
    ];
}


function toUserResponse($data)
{
    return [
        'name' => $data['name'],
        'email' => $data['email'],
        'role' => $data['role'],
        'phone' => $data['phone'],
    ];
}

function toTokenResponse($data)
{
    return [
        'accessToken' => $data['accessToken']
    ];
}
function responseSuccess(string $message, $data, int $statusCode = 200)
{
    return [
        'success' => true,
        'message' => $message,
        'data' => $data,
        'errors' => null
    ];
}
function responseError(string $message, $details, $code)
{
    return [
        'success' => false,
        'message' => $message,
        'data' => null,
        'errors' => [
            'code' => $code,
            'details' => $details,
        ]
    ];
}
function getUser()
{
    return User::where('email', 'test@gmail.com')
        ->select(['id', 'name', 'email', 'phone', 'role'])
        ->first()->toArray();
}
function createToken(string $name)
{
    $user = User::where('email', 'test@gmail.com')
        ->first();
    $token = $user->createToken($name)->plainTextToken;
    return $token;
}

function getToken()
{
    return PersonalAccessToken::limit(1)->first();
}
