<?php

use Database\Seeders\UserSeeder;

use function Pest\Laravel\post;

describe('register user', function () {
    test('success', function () {
        $data = [
            'name' => 'Muhammad Murtadlo',
            'email' => 'muh.murtadlo23@gmail.com',
            'password' => 'apikwid123',
            'role' => 'NURSE',
            'phone' => '08214780323'
        ];
        post('/api/users/register', $data)
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
        post('/api/users/register', $data)
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
        post('/api/users/register', $data)
            ->assertConflict()
            ->assertJson([
                'errors' => ['messages ' => ['email sudah digunakan!']]
            ]);
    });
});

describe('user login', function () {
    test('success', function () {
        $this->seed([UserSeeder::class]);

        post('/api/users/login', [
            'email' => 'test@gmail.com',
            'password' => 'testtesttest'
        ])->assertOk()->assertJson([
            'data' => [
                'name' => 'test',
                'email' => 'test@gmail.com',
                'phone' => '0822141454',
                'role' => 'ADMIN'
            ]
        ]);
    });
    test('failed cause email is wrong', function () {
        $this->seed([UserSeeder::class]);

        post('/api/users/login', [
            'email' => 'test1@gmail.com',
            'password' => 'testtesttest'
        ])->assertNotFound()->assertJson([
            'errors' => ['messages ' => ['email atau password salah!']]
        ]);
    });
    test('failed cause password is wrong', function () {
        $this->seed([UserSeeder::class]);

        post('/api/users/login', [
            'email' => 'test@gmail.com',
            'password' => 'salahslaah'
        ])->assertNotFound()->assertJson([
            'errors' => ['messages ' => ['email atau password salah!']]
        ]);
    });
})->only();
