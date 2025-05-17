<?php

use App\Models\User;
use Database\Seeders\UserSeeder;
use Laravel\Sanctum\PersonalAccessToken;

use function Pest\Laravel\post;
use function PHPUnit\Framework\assertEquals;

describe('register user', function () {
    test('success', function () {
        $data = [
            'name' => 'Muhammad Murtadlo',
            'email' => 'muh.murtadlo23@gmail.com',
            'password' => 'apikwid123',
            'role' => 'NURSE',
            'phone' => '08214780323'
        ];
        post('/api/auth/register', $data)
            ->assertCreated()->assertJson([
                'data' => [
                    'name' => 'Muhammad Murtadlo',
                    'email' => 'muh.murtadlo23@gmail.com',
                    'role' => 'NURSE',
                    'phone' => '08214780323'
                ]
            ]);
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
            ->assertBadRequest()
            ->assertJson([
                'errors' => [
                    'name' => ["The name field is required."],
                    'email' => ["The email field must be a valid email address."],
                    'password' => ["The password field must be at least 8 characters."],
                    'role' => ["The selected role is invalid."],
                    'phone' => ["The phone field must be at least 10 characters."]
                ]
            ]);
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
            ->assertConflict()
            ->assertJson([
                'errors' => ['messages ' => ['email sudah digunakan!']]
            ]);
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
            ->assertSee('tokenAccess');
        $token = getToken();
        assertEquals($user['id'], $token->tokenable_id);
        assertEquals('vivo', $token->name);
    });
    test('failed cause email is wrong', function () {
        $this->seed([UserSeeder::class]);

        post('/api/auth/login', [
            'email' => 'test1@gmail.com',
            'password' => 'testtesttest'
        ])->assertUnauthorized()->assertJson([
            'errors' => ['messages ' => ['email atau password salah!']]
        ]);
    });
    test('failed cause password is wrong', function () {
        $this->seed([UserSeeder::class]);

        post('/api/auth/login', [
            'email' => 'test@gmail.com',
            'password' => 'salahslaah'
        ])->assertUnauthorized()->assertJson([
            'errors' => ['messages ' => ['email atau password salah!']]
        ]);
    });
    test('failed cause password request is empty', function () {
        $this->seed([UserSeeder::class]);

        post('/api/auth/login', [
            'email' => 'test@gmail.com',
            'password' =>  ''
        ])->assertBadRequest()->assertJson([
            'errors' => ['password' => ["The password field is required."]]
        ]);
    });
    test('failed cause email request is invalid', function () {
        $this->seed([UserSeeder::class]);

        post('/api/auth/login', [
            'email' => 'test',
            'password' =>  'testtesttest'
        ])->assertBadRequest()->assertJson([
            'errors' => ['email' => ['The email field must be a valid email address.']]
        ]);
    });
});






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
