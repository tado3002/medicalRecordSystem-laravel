<?php

use App\Http\Resources\UserResource;
use App\Models\User;
use Database\Seeders\UserCollectionSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Laravel\Sanctum\PersonalAccessToken;

use function Pest\Laravel\delete;
use function Pest\Laravel\get;
use function Pest\Laravel\put;
use function PHPUnit\Framework\assertEquals;

describe('user profile', function () {
    it('success', function () {
        $this->seed([UserSeeder::class]);
        $user = getUser();

        $token = createToken('device_vivo');
        $expected = responseSuccess('Berhasil mendapatkan data!', $user);

        get('/api/users/profile', [
            'Authorization' => "Bearer $token",
        ])->assertOk()->assertJson($expected);
    });
    it('failed cause token is invalid', function () {
        $this->seed([UserSeeder::class]);
        createToken('testing');

        $expected = responseError('Unauthenticated!', null, 'INVALID_CREDENTIALS');
        get('/api/users/profile', [
            'Authorization' => "Bearer salah",
            'Accept' => 'application/json'
        ])
            ->assertUnauthorized()
            ->assertJson($expected);
    });
    it('failed cause header not set', function () {
        $this->seed([UserSeeder::class]);
        createToken('testing');

        $expected = responseError('Unauthenticated!', null, 'INVALID_CREDENTIALS');
        get('/api/users/profile', [
            'Accept' => 'application/json'
        ])
            ->assertUnauthorized()
            ->assertJson($expected);
    });
});

describe('user find by id', function () {
    it('success', function () {
        $this->seed([UserSeeder::class]);
        $user = getUser();

        $token = createToken('device_vivo');
        $expected = responseSuccess('Berhasil mendapatkan data!', $user);

        get('/api/users/' . $user['id'], [
            'Authorization' => "Bearer $token",
        ])->assertOk()->assertJson($expected);
    });
    it('failed cause user id not found', function () {
        $this->seed([UserSeeder::class]);
        $user = getUser();
        $token = createToken('testing');

        $userId = $user['id'] + 1;
        $expected = responseError('User tidak ditemukan!', null, 'NOT_FOUND');
        get('/api/users/' . $userId, [
            'Authorization' => "Bearer $token",
            'Accept' => 'application/json'
        ])
            ->assertNotFound()
            ->assertJson($expected);
    });
    it('failed cause token is invalid', function () {
        $this->seed([UserSeeder::class]);
        $user = getUser();
        createToken('testing');

        $expected = responseError('Unauthenticated!', null, 'INVALID_CREDENTIALS');
        get('/api/users/' . $user['id'], [
            'Authorization' => "Bearer salah",
            'Accept' => 'application/json'
        ])
            ->assertUnauthorized()
            ->assertJson($expected);
    });
    it('failed cause header not set', function () {
        $this->seed([UserSeeder::class]);
        $user = getUser();
        createToken('testing');

        $expected = responseError('Unauthenticated!', null, 'INVALID_CREDENTIALS');
        get('/api/users/' . $user['id'], headers: [
            'Accept' => 'application/json'
        ])
            ->assertUnauthorized()
            ->assertJson($expected);
    });
});

describe('user paginate search by param [role,name]', function () {
    it('success pagination search default', function () {
        $this->seed([UserSeeder::class]);
        $this->seed([UserCollectionSeeder::class]);

        $token = createToken('device_vivo');
        $userCollection = getUserCollectionResponse();

        $res = get('/api/users/search', [
            'Authorization' => "Bearer $token",
        ])->assertOk()->json();

        assertEquals($userCollection['data']['items'], $res['data']['items']);
    });

    it('success pagination search by page 2 and size 20', function () {
        $this->seed([UserSeeder::class]);
        $this->seed([UserCollectionSeeder::class]);

        $token = createToken('device_vivo');
        $userCollection = getUserCollectionResponse([
            'page' => 2,
            'size' => 20
        ]);

        $res = get('/api/users/search?page=2&&size=20', [
            'Authorization' => "Bearer $token",
        ])->assertOk()->json();

        assertEquals($userCollection['data']['items'], $res['data']['items']);
    });
    it('success pagination search by role is ADMIN', function () {
        $this->seed([UserSeeder::class]);
        $this->seed([UserCollectionSeeder::class]);

        $token = createToken('device_vivo');
        $userCollection = getUserCollectionResponse([
            'role' => 'ADMIN'
        ]);

        $res = get('/api/users/search?role=ADMIN', [
            'Authorization' => "Bearer $token",
        ])->assertOk()->json();

        // cek setiap item memiliki role ADMIN
        foreach ($res['data']['items'] as $key => $value) {
            assertEquals($value['role'], 'ADMIN');
        };

        assertEquals($userCollection['data']['items'], $res['data']['items']);
    });
    it('success pagination search by role is DOCTER', function () {
        $this->seed([UserSeeder::class]);
        $this->seed([UserCollectionSeeder::class]);

        $token = createToken('device_vivo');
        $userCollection = getUserCollectionResponse([
            'role' => 'DOCTER'
        ]);

        $res = get('/api/users/search?role=DOCTER', [
            'Authorization' => "Bearer $token",
        ])->assertOk()->json();

        // cek setiap item memiliki role ADMIN
        foreach ($res['data']['items'] as $key => $value) {
            assertEquals($value['role'], 'DOCTER');
        };

        assertEquals($userCollection['data']['items'], $res['data']['items']);
    });
    it('success pagination search by role is NURSE', function () {
        $this->seed([UserSeeder::class]);
        $this->seed([UserCollectionSeeder::class]);

        $token = createToken('device_vivo');
        $userCollection = getUserCollectionResponse([
            'role' => 'NURSE'
        ]);

        $res = get('/api/users/search?role=NURSE', [
            'Authorization' => "Bearer $token",
        ])->assertOk()->json();

        // cek setiap item memiliki role ADMIN
        foreach ($res['data']['items'] as $key => $value) {
            assertEquals($value['role'], 'NURSE');
        };

        assertEquals($userCollection['data']['items'], $res['data']['items']);
    });
    it('success pagination search by name', function () {
        $this->seed([UserSeeder::class]);
        $this->seed([UserCollectionSeeder::class]);

        $token = createToken('device_vivo');
        $userName = User::first('name');

        $userCollection = getUserCollectionResponse([
            'name' => $userName
        ]);


        $res = get('/api/users/search?name=' . $userName, [
            'Authorization' => "Bearer $token",
        ])->assertOk()->json();


        assertEquals($userCollection['data']['items'], $res['data']['items']);
    });
    it('failed cause user id not found', function () {
        $this->seed([UserSeeder::class]);
        $user = getUser();
        $token = createToken('testing');

        $userId = $user['id'] + 1;
        $expected = responseError('User tidak ditemukan!', null, 'NOT_FOUND');
        get('/api/users/' . $userId, [
            'Authorization' => "Bearer $token",
            'Accept' => 'application/json'
        ])
            ->assertNotFound()
            ->assertJson($expected);
    });
    it('failed cause token is invalid', function () {
        $this->seed([UserSeeder::class]);
        $user = getUser();
        createToken('testing');

        $expected = responseError('Unauthenticated!', null, 'INVALID_CREDENTIALS');
        get('/api/users/' . $user['id'], [
            'Authorization' => "Bearer salah",
            'Accept' => 'application/json'
        ])
            ->assertUnauthorized()
            ->assertJson($expected);
    });
    it('failed cause header not set', function () {
        $this->seed([UserSeeder::class]);
        $user = getUser();
        createToken('testing');

        $expected = responseError('Unauthenticated!', null, 'INVALID_CREDENTIALS');
        get('/api/users/' . $user['id'], headers: [
            'Accept' => 'application/json'
        ])
            ->assertUnauthorized()
            ->assertJson($expected);
    });
});

describe('update user', function () {
    it('success', function () {
        $this->seed([UserSeeder::class]);
        $user = getUser();
        $token = createToken('device_vivo');

        $emailBaru = 'newEmail@gmail.com';
        $user['email'] = $emailBaru;

        $expected = responseSuccess('Berhasil mengupdate data!', $user);

        put(
            "/api/users/{$user['id']}",
            ['email' => $emailBaru],
            headers: ['Authorization' => "Bearer $token"]
        )
            ->assertOk()
            ->assertJson($expected);
    });
    it('conflict error cause email has use by another user', function () {
        $this->seed([UserSeeder::class]);
        $user = getUser();
        $token = createToken('device_vivo');

        $user['email'] = 'iniemailsama@gmail.com';
        $user['password'] = 'iniemailsama';
        createUser($user);

        $expected = responseError('User request tidak valid!', [
            'email' => ['The email has already been taken.']
        ], 'REQUEST_INVALID');

        put(
            "/api/users/{$user['id']}",
            ['email' => $user['email']],
            headers: ['Authorization' => "Bearer $token"]
        )
            ->assertUnprocessable()
            ->assertJson($expected);
    });
    it('not found error cause user id not exist', function () {
        $this->seed([UserSeeder::class]);
        $user = getUser();
        $token = createToken('device_vivo');
        $expected = responseError('User tidak ditemukan!', null, 'NOT_FOUND');

        $userId = $user['id'] + 1;
        put(
            "/api/users/{$userId}",
            ['phone' => '082146966594'],
            headers: ['Authorization' => "Bearer $token"]
        )
            ->assertNotFound()
            ->assertJson($expected);
    });
    it('failed cause role not admin', function () {
        $this->seed([UserSeeder::class]);
        $user = getUser();
        // update role to NURSE
        $updatedUser = User::where('email', $user['email'])->first();
        $updatedUser->role = 'NURSE';
        $updatedUser->save();

        $token = createToken('device_vivo');

        $expected = responseError('Hanya untuk role ADMIN!', null, 'FORBIDDEN');
        put(
            "/api/users/{$user['id']}",
            ['email' => $user['email']],
            headers: [
                'Authorization' => "Bearer $token",
                'Accept' => 'application/json'
            ]
        )
            ->assertForbidden()
            ->assertJson($expected);
    });
    it('failed cause token not set', function () {
        $this->seed([UserSeeder::class]);
        $user = getUser();

        $expected = responseError('Unauthenticated!', null, 'INVALID_CREDENTIALS');

        $user['phone'] = '081330329363';
        put(
            "/api/users/{$user['id']}",
            ['email' => $user['email']],
            ['Accept' => 'application/json']

        )
            ->assertUnauthorized()
            ->assertJson($expected);
    });
});

describe('delete user', function () {
    it('success', function () {
        $this->seed([UserSeeder::class]);
        $user = getUser();
        $token = createToken('device_vivo');

        $expected = responseSuccess('Berhasil menghapus data!', $user);

        delete(
            "/api/users/{$user['id']}",
            headers: ['Authorization' => "Bearer $token"]
        )
            ->assertOk()
            ->assertJson($expected);
    });
    it('not found error cause user id not exist', function () {
        $this->seed([UserSeeder::class]);
        $user = getUser();
        $token = createToken('device_vivo');
        $expected = responseError('User tidak ditemukan!', null, 'NOT_FOUND');

        $userId = $user['id'] + 1;
        delete(
            "/api/users/{$userId}",
            headers: ['Authorization' => "Bearer $token"]
        )
            ->assertNotFound()
            ->assertJson($expected);
    });
    it('failed cause role not admin', function () {
        $this->seed([UserSeeder::class]);
        $user = getUser();
        // update role to NURSE
        $updatedUser = User::where('email', $user['email'])->first();
        $updatedUser->role = 'NURSE';
        $updatedUser->save();

        $token = createToken('device_vivo');

        $expected = responseError('Hanya untuk role ADMIN!', null, 'FORBIDDEN');
        delete(
            "/api/users/{$user['id']}",
            headers: [
                'Authorization' => "Bearer $token",
                'Accept' => 'application/json'
            ]
        )
            ->assertForbidden()
            ->assertJson($expected);
    });
    it('failed cause token not set', function () {
        $this->seed([UserSeeder::class]);
        $user = getUser();

        $expected = responseError('Unauthenticated!', null, 'INVALID_CREDENTIALS');

        delete(
            "/api/users/{$user['id']}",
            headers: ['Accept' => 'application/json']

        )
            ->assertUnauthorized()
            ->assertJson($expected);
    });
});


function getUserCollectionResponse(array $params = [])
{
    $page = $params['page'] ?? 1;
    $size = $params['size'] ?? 10;

    $users = User::where(function (Builder $builder) use ($params) {
        $name = $params['name'] ?? null;
        $role = $params['role'] ?? null;

        if ($role) $builder->where('role', $role);
        if ($name) $builder->where('name', "%$name%");
    })
        ->paginate(perPage: $size, page: $page);

    return responsePaginate('Berhasil mendapatkan data!', UserResource::collection($users));
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
function responseSuccess(string $message, $data, int $statusCode = 200)
{
    return [
        'success' => true,
        'message' => $message,
        'data' =>  toUserResponse($data),
        'errors' => null
    ];
}

function fixLink($str)
{
    return $str ? str_replace('?', '/users?', $str) : null;
}
function responsePaginate($message, $data)
{
    return [
        'success' => true,
        'message' => $message,
        'data' => [
            'items' => $data->toArray(request()),
            'page' => [
                'total' => $data->total(),
                'per_page' => $data->perPage(),
                'current_page' => $data->currentPage(),
                'total_page' => $data->lastPage(),
                'links' => [
                    'first' => fixLink($data->url(1)),
                    'last' => fixLink($data->url($data->lastPage())),
                    'prev' => fixLink($data->previousPageUrl()),
                    'next' => fixLink($data->nextPageUrl()),
                ]
            ]
        ],
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
function createUser($data)
{
    return User::create($data);
}
