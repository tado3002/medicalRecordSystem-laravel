<?php

use App\Http\Resources\DocterCollection;
use App\Http\Resources\DocterResource;
use App\Models\Docter;
use App\Models\User;
use Database\Seeders\DocterCollectionSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Contracts\Database\Eloquent\Builder;

use function Pest\Laravel\delete;
use function Pest\Laravel\get;
use function Pest\Laravel\post;
use function Pest\Laravel\put;
use function PHPSTORM_META\map;
use function PHPUnit\Framework\assertEquals;

describe('create docter', function () {
    $dataUser = [
        'name' => 'docter1',
        'email' => 'docter@gmail.com',
        'password' => 'docter123',
        'phone' => '08214666953',
        'role' => 'DOCTER',
    ];
    test('success', function () use ($dataUser) {
        $this->seed([UserSeeder::class]);
        $token = getToken();
        $user = createUser($dataUser);

        $res = post('/api/docters', [
            'user_id' => $user->id,
            'specialization' => 'Dokter mata'
        ], [
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json'
        ])->assertCreated()->json();

        $docter = getDocter($res['data']['id']);
        $expected = successResponse(
            'Berhasil menambahkan data!',
            $docter
        );
        assertEquals($expected, $res);
    });
    test('failed cause invalid query', function () use ($dataUser) {
        $this->seed([UserSeeder::class]);
        $dataUser = $dataUser;

        $token = getToken();
        $user = createUser($dataUser);

        post('/api/docters', [
            'user_id' => $user->id,
            'specialization' => ''
        ], [
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json'
        ])->assertUnprocessable()->assertJson(
            failedResponse('User request tidak valid!', [
                'specialization' => ["The specialization field is required."]

            ], 'INVALID_REQUEST')
        );
    });
    test('failed cause role is not ADMIN', function () use ($dataUser) {
        createUser([
            'name' => 'admin12',
            'email' => 'admin12@gmail.com',
            'password' => 'admin12',
            'role' => 'NURSE',
            'phone' => '0821473292',
        ]);
        $token = getToken();
        $user = createUser($dataUser);

        post('/api/docters', [
            'user_id' => $user->id,
            'specialization' => 'Dokter mata'
        ], [
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json'
        ])->assertForbidden()->assertJson(
            failedResponse('Hanya untuk role ADMIN!', null, 'FORBIDDEN')
        );
    });
    test('failed cause role user to create docter is not DOCTER', function () use ($dataUser) {
        $this->seed([UserSeeder::class]);
        $token = getToken();
        $user = createUser([...$dataUser, 'role' => 'NURSE']);

        post('/api/docters', [
            'user_id' => $user->id,
            'specialization' => 'Dokter mata'
        ], [
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json'
        ])->assertForbidden()->assertJson(
            failedResponse('Id tidak terdaftar sebagai DOCTER!', null, 'FORBIDDEN')
        );
    });
    test('failed token not set in header', function () use ($dataUser) {
        $this->seed([UserSeeder::class]);
        $token = getToken();
        $user = createUser([...$dataUser, 'role' => 'NURSE']);

        post('/api/docters', [
            'user_id' => $user->id,
            'specialization' => 'Dokter mata'
        ], [
            'Accept' => 'application/json'
        ])->assertUnauthorized()->assertJson(
            failedResponse('Unauthenticated!', null, 'INVALID_CREDENTIALS')
        );
    });
    test('failed token is invalid', function () use ($dataUser) {
        $this->seed([UserSeeder::class]);
        $token = getToken();
        $user = createUser([...$dataUser, 'role' => 'NURSE']);

        post('/api/docters', [
            'user_id' => $user->id,
            'specialization' => 'Dokter mata'
        ], [
            'Authorization' => 'Bearer salah',
            'Accept' => 'application/json'
        ])->assertUnauthorized()->assertJson(
            failedResponse('Unauthenticated!', null, 'INVALID_CREDENTIALS')
        );
    });
});
describe('find docter by id', function () {

    test('success', function () {
        $this->seed([UserSeeder::class]);
        $token = getToken();
        $docter = createDocter();

        $res = get('/api/docters/' . $docter->id, [
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json'
        ])->assertOK()->json();

        $docter = getDocter($res['data']['id']);
        $expected = successResponse(
            'Berhasil mendapatkan data!',
            $docter
        );
        assertEquals($expected, $res);
    });
    test('failed cause docter id not exist', function () {
        $this->seed([UserSeeder::class]);

        $token = getToken();
        $docter = createDocter();

        get('/api/docters/' . $docter->id + 1, [
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json'
        ])->assertNotFound()->assertJson(
            failedResponse('Spesialisasi dokter tidak ditemukan!', null, 'NOT_FOUND')
        );
    });
    test('failed token not set in header', function () {
        $this->seed([UserSeeder::class]);
        $token = getToken();
        $docter = createDocter();


        get('/api/docters/' . $docter->id, [
            'Accept' => 'application/json'
        ])->assertUnauthorized()->assertJson(
            failedResponse('Unauthenticated!', null, 'INVALID_CREDENTIALS')
        );
    });
    test('failed token is invalid', function () {
        $this->seed([UserSeeder::class]);
        $token = getToken();
        $docter = createDocter();

        get('/api/docters/' . $docter->id, [
            'Authorization' => 'Bearer salah',
            'Accept' => 'application/json'
        ])->assertUnauthorized()->assertJson(
            failedResponse('Unauthenticated!', null, 'INVALID_CREDENTIALS')
        );
    });
});
describe('update docter', function () {
    test('success', function () {
        $this->seed([UserSeeder::class]);
        $token = getToken();
        $docter = createDocter();

        $res = put('/api/docters/' . $docter->id, [
            'specialization' => 'Dokter hewan'
        ], [
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json'
        ])->assertOK()->json();

        $docter = getDocter($res['data']['id']);
        $expected = successResponse(
            'Berhasil mengupdate data!',
            $docter
        );
        assertEquals($expected, $res);
    });
    test('failed cause invalid query', function () {
        $this->seed([UserSeeder::class]);

        $token = getToken();
        $docter = createDocter();

        put('/api/docters/' . $docter->id, [
            'specialization' => ''
        ], [
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json'
        ])->assertUnprocessable()->assertJson(
            failedResponse('User request tidak valid!', [
                'specialization' => ["The specialization field is required."]

            ], 'INVALID_REQUEST')
        );
    });
    test('failed cause docter id not found', function () {
        $this->seed([UserSeeder::class]);

        $token = getToken();
        $docter = createDocter();

        put('/api/docters/' . $docter->id + 1, [
            'specialization' => 'Dokter Tulang'
        ], [
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json'
        ])->assertNotFound()->assertJson(
            failedResponse(
                'Spesialisasi dokter tidak ditemukan!',
                null,
                'NOT_FOUND'
            )
        );
    });
    test('failed cause role is not ADMIN', function () {
        createUser([
            'name' => 'admin12',
            'email' => 'admin12@gmail.com',
            'password' => 'admin12',
            'role' => 'NURSE',
            'phone' => '0821473292',
        ]);
        $token = getToken();
        $docter = createDocter();

        put('/api/docters/' . $docter->id, [
            'specialization' => 'Dokter mata'
        ], [
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json'
        ])->assertForbidden()->assertJson(
            failedResponse('Hanya untuk role ADMIN!', null, 'FORBIDDEN')
        );
    });
    test('failed token not set in header', function () {
        $this->seed([UserSeeder::class]);
        $token = getToken();
        $docter = createDocter();

        put('/api/docters/' . $docter->id, [
            'specialization' => 'Dokter hidung'
        ], [
            'Accept' => 'application/json'
        ])->assertUnauthorized()->assertJson(
            failedResponse('Unauthenticated!', null, 'INVALID_CREDENTIALS')
        );
    });
    test('failed token is invalid', function () {
        $this->seed([UserSeeder::class]);
        $token = getToken();
        $docter = createDocter();

        put('/api/docters/' . $docter->id, [
            'specialization' => 'Dokter mata'
        ], [
            'Authorization' => 'Bearer salah',
            'Accept' => 'application/json'
        ])->assertUnauthorized()->assertJson(
            failedResponse('Unauthenticated!', null, 'INVALID_CREDENTIALS')
        );
    });
});
describe('delete docter', function () {
    test('success', function () {
        $this->seed([UserSeeder::class]);
        $token = getToken();
        $docter = createDocter();

        $createdDocter = getDocter($docter->id);

        $res = delete(
            '/api/docters/' . $docter->id,
            headers: [
                'Authorization' => 'Bearer ' . $token,
                'Accept' => 'application/json'
            ]
        )->assertOK()->json();

        $expected = successResponse(
            'Berhasil menghapus data!',
            $createdDocter
        );
        assertEquals($expected, $res);
    });
    test('failed cause docter id not found', function () {
        $this->seed([UserSeeder::class]);

        $token = getToken();
        $docter = createDocter();

        delete(
            '/api/docters/' . $docter->id + 1,
            headers: [
                'Authorization' => 'Bearer ' . $token,
                'Accept' => 'application/json'
            ]
        )->assertNotFound()->assertJson(
            failedResponse(
                'Spesialisasi dokter tidak ditemukan!',
                null,
                'NOT_FOUND'
            )
        );
    });
    test('failed cause role is not ADMIN', function () {
        createUser([
            'name' => 'admin12',
            'email' => 'admin12@gmail.com',
            'password' => 'admin12',
            'role' => 'NURSE',
            'phone' => '0821473292',
        ]);
        $token = getToken();
        $docter = createDocter();

        delete(
            '/api/docters/' . $docter->id,
            headers: [
                'Authorization' => 'Bearer ' . $token,
                'Accept' => 'application/json'
            ]
        )->assertForbidden()->assertJson(
            failedResponse('Hanya untuk role ADMIN!', null, 'FORBIDDEN')
        );
    });
    test('failed token not set in header', function () {
        $this->seed([UserSeeder::class]);
        $token = getToken();
        $docter = createDocter();

        delete(
            '/api/docters/' . $docter->id,
            headers: [
                'Accept' => 'application/json'
            ]
        )->assertUnauthorized()->assertJson(
            failedResponse('Unauthenticated!', null, 'INVALID_CREDENTIALS')
        );
    });
    test('failed token is invalid', function () {
        $this->seed([UserSeeder::class]);
        $token = getToken();
        $docter = createDocter();

        delete(
            '/api/docters/' . $docter->id,
            headers: [
                'Authorization' => 'Bearer salah',
                'Accept' => 'application/json'
            ]
        )->assertUnauthorized()->assertJson(
            failedResponse('Unauthenticated!', null, 'INVALID_CREDENTIALS')
        );
    });
});
describe('search docter pagination', function () {

    test('success search default', function () {
        $this->seed([UserSeeder::class, DocterCollectionSeeder::class]);
        $token = getToken();
        $docter = searchDocter();

        $res = get('/api/docters/search', [
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json'
        ])->assertOK()->json();

        $expected = successResponse(
            'Berhasil mendapatkan data!',
            $docter
        );
        assertEquals($expected['data']['items'], $res['data']['items']);
    });
    test('success search by param page 2 size 20', function () {
        $this->seed([UserSeeder::class, DocterCollectionSeeder::class]);
        $token = getToken();
        $docter = searchDocter([
            'page' => 2,
            'size' => 20
        ]);

        $res = get('/api/docters/search?page=2&&size=20', [
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json'
        ])->assertOK()->json();

        $expected = successResponse(
            'Berhasil mendapatkan data!',
            $docter
        );
        assertEquals($expected['data']['items'], $res['data']['items']);
    });
    test('success search docter by name', function () {
        $this->seed([UserSeeder::class, DocterCollectionSeeder::class]);
        $token = getToken();
        $docter = searchDocter([
            'name' => 'Mr'
        ]);

        $res = get('/api/docters/search?name=Mr', [
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json'
        ])->assertOK()->json();

        $expected = successResponse(
            'Berhasil mendapatkan data!',
            $docter
        );
        assertEquals($expected['data']['items'], $res['data']['items']);
    });
    test('success search docter by specialization', function () {
        $this->seed([UserSeeder::class, DocterCollectionSeeder::class]);
        $token = getToken();
        $docter = searchDocter([
            'specialization' => 'Saraf'
        ]);

        $res = get('/api/docters/search?specialization=saraf', [
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json'
        ])->assertOK()->json();

        $expected = successResponse(
            'Berhasil mendapatkan data!',
            $docter
        );
        assertEquals($expected['data']['items'], $res['data']['items']);
    });
    test('success search docter by name and specialization', function () {
        $this->seed([UserSeeder::class, DocterCollectionSeeder::class]);
        $token = getToken();
        $docter = searchDocter([
            'name' => 'dr',
            'specialization' => 'Saraf'
        ]);

        $res = get('/api/docters/search?name=dr&&specialization=saraf', [
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json'
        ])->assertOK()->json();

        $expected = successResponse(
            'Berhasil mendapatkan data!',
            $docter
        );
        assertEquals($expected['data']['items'], $res['data']['items']);
    });
    test('failed token not set in header', function () {
        $this->seed([UserSeeder::class]);
        $token = getToken();


        get('/api/docters/search', [
            'Accept' => 'application/json'
        ])->assertUnauthorized()->assertJson(
            failedResponse('Unauthenticated!', null, 'INVALID_CREDENTIALS')
        );
    });
    test('failed token is invalid', function () {
        $this->seed([UserSeeder::class]);
        $token = getToken();

        get('/api/docters/search', [
            'Authorization' => 'Bearer salah',
            'Accept' => 'application/json'
        ])->assertUnauthorized()->assertJson(
            failedResponse('Unauthenticated!', null, 'INVALID_CREDENTIALS')
        );
    });
});


function createUser(array $data): User
{
    return User::create($data);
}

function createDocter($userId = null): Docter
{
    $userId = $userId ?? createUser([
        'name' => 'docter1',
        'email' => 'docter@gmail.com',
        'password' => 'docter123',
        'phone' => '08214666953',
        'role' => 'DOCTER',
    ])->id;
    $docter = Docter::create([
        'user_id' => $userId,
        'specialization' => 'Dokter mata'
    ]);

    return $docter;
}

function searchDocter($params = [])
{
    $page = $params['page'] ?? 1;
    $size = $params['size'] ?? 10;
    $docters = Docter::where(function (Builder $builder) use ($params) {
        $specialization = $params['specialization'] ?? null;
        $name = $params['name'] ?? null;
        if (!empty($specialization)) $builder->where('specialization', 'like', "%$specialization%");
        if (!empty($name)) $builder->whereHas('user', function ($query) use ($name) {
            $query->where('name', 'like', "%$name%");
        });
    });
    $docters = $docters->paginate($size, page: $page);
    $docterCollection = new DocterCollection($docters);
    return $docterCollection->toResponse(request())->getData(true);
}

function getDocter($id)
{
    $docter = Docter::where('id', $id)->first();
    $docter = new DocterResource($docter);
    return $docter->toResponse(request())->getData(true);
}
function getToken()
{
    $user = User::limit(1)->first();
    $token = $user->createToken('testing')->plainTextToken;
    return $token;
}
function successResponse($message, $data)
{
    return [
        'success' => true,
        'data' => $data['data'],
        'errors' => null,
        'message' => $message
    ];
}
function failedResponse($message, $details, $code)
{
    return [
        'success' => false,
        'data' => null,
        'message' => $message,
        'errors' => [
            'code' => $code,
            'details' => $details
        ],
    ];
}
