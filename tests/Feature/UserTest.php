<?php

use App\Models\User;
use Database\Seeders\UserSeeder;
use Laravel\Sanctum\PersonalAccessToken;

use function Pest\Laravel\get;
use function Pest\Laravel\post;


describe('user profile', function () {
    it('success', function () {
        $this->seed([UserSeeder::class]);
        $user = getUser();

        $response = post('/api/users/login', [
            'email' => $user['email'],
            'password' => 'testtesttest',
            'device_name' => 'vivo'
        ]);

        $token = $response->json('data.tokenAccess');
        get('/api/users/profile', [
            'Authorization' => "Bearer $token",
        ])->assertOk()->assertJson([
            'data' => $user
        ]);
    });
    it('failed cause token is invalid', function () {
        $this->seed([UserSeeder::class]);
        createToken('testing');

        get('/api/users/profile', [
            'Authorization' => "Bearer salah",
            'Accept' => 'application/json'
        ])
            ->assertUnauthorized()
            ->assertJson([
                'errors' => [
                    'messages' => ['Unauthorized!']
                ]
            ]);
    });
    it('failed cause header not set', function () {
        $this->seed([UserSeeder::class]);
        get('/api/users/profile', ['Accept' => 'application/json'])
            ->assertUnauthorized()
            ->assertJson([
                'errors' => [
                    'messages' => ['Unauthorized!']
                ]
            ]);
    });
})->only();


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
