<?php

use App\Models\Patient;
use App\Models\User;
use Database\Seeders\PatientSeeder;

use function Pest\Laravel\delete;
use function Pest\Laravel\get;
use function Pest\Laravel\post;
use function Pest\Laravel\put;
use function PHPUnit\Framework\assertEmpty;
use function PHPUnit\Framework\assertEquals;

describe('create patient test', function () {
    $dataPatient = [
        'name' => 'Adolf Hitler',
        'nik' => '3514203108030003',
        'gender' => 'male',
        'birthday' => '2003-08-31',
        'address' => 'Ds. Sumberdawesari',
        'phone' => '082146796695',
        'emergency_phone' => '087849458085'
    ];
    test('success create patient', function () use ($dataPatient) {
        $token = getToken();
        post('/api/patients', $dataPatient, [
            'Authorization' => 'Bearer ' . $token
        ])->assertCreated()->assertJson(successResponse('Berhasil menambahkan data!', $dataPatient));
    });
    test('conflict error response cause nik is exist ', function () use ($dataPatient) {
        $token = getToken();
        createPatient($dataPatient);
        post('/api/patients', $dataPatient, [
            'Authorization' => 'Bearer ' . $token
        ])->assertConflict()->assertJson(failedResponse('NIK sudah digunakan!', null, 'CONFLICT_ERROR'));
    });
    test('bad request error response cause request not valid ', function () use ($dataPatient) {
        $token = getToken();
        $dataPatient['name'] = '';
        $invalidFields = [
            'name' => ['The name field is required.']
        ];
        post('/api/patients', $dataPatient, [
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json'
        ])->assertBadRequest()->assertJson(failedResponse('User request tidak valid!', $invalidFields, 'BAD_REQUEST'));
    });
    test('unauthenticated error response cause token not set ', function () use ($dataPatient) {
        post('/api/patients', $dataPatient, [
            'Accept' => 'application/json'
        ])->assertUnauthorized()->assertJson(failedResponse('Unauthenticated!', null, 'INVALID_CREDENTIALS'));
    });
    test('unauthenticated error response cause token is invalid ', function () use ($dataPatient) {
        $token = getToken();
        post('/api/patients', $dataPatient, [
            'Authorization' => 'Bearer salah',
            'Accept' => 'application/json'
        ])->assertUnauthorized()->assertJson(failedResponse('Unauthenticated!', null, 'INVALID_CREDENTIALS'));
    });
});

describe('get patient by id', function () {
    $dataPatient = [
        'name' => 'Adolf Hitler',
        'nik' => '3514203108030003',
        'gender' => 'male',
        'birthday' => '2003-08-31',
        'address' => 'Ds. Sumberdawesari',
        'phone' => '082146796695',
        'emergency_phone' => '087849458085'
    ];
    test('success get patient by id', function () use ($dataPatient) {
        $token = getToken();
        $patient = createPatient($dataPatient);
        get("/api/patients/{$patient['id']}", [
            'Authorization' => 'Bearer ' . $token
        ])->assertOk()->assertJson(successResponse('Berhasil mendapatkan data!', $dataPatient));
    });
    test('error not found cause id patient not exist', function () use ($dataPatient) {
        $token = getToken();
        $patient = createPatient($dataPatient);
        $patient['id'] = $patient['id'] + 1;
        get("/api/patients/{$patient['id']}", [
            'Authorization' => 'Bearer ' . $token
        ])->assertNotFound()->assertJson(failedResponse('Patient tidak ditemukan!', null, 'NOT_FOUND'));
    });
    test('unauthenticated error response cause token not set ', function () use ($dataPatient) {
        $patient = createPatient($dataPatient);
        get("/api/patients/{$patient['id']}", [
            'Accept' => 'application/json'
        ])->assertUnauthorized()->assertJson(failedResponse('Unauthenticated!', null, 'INVALID_CREDENTIALS'));
    });
    test('unauthenticated error response cause token is invalid ', function () use ($dataPatient) {
        $patient = createPatient($dataPatient);
        $token = getToken();
        get("/api/patients/{$patient['id']}", [
            'Authorization' => 'Bearer salah',
            'Accept' => 'application/json'
        ])->assertUnauthorized()->assertJson(failedResponse('Unauthenticated!', null, 'INVALID_CREDENTIALS'));
    });
});

describe('update patient', function () {
    $dataPatient = [
        'name' => 'Adolf Hitler',
        'nik' => '3514203108030003',
        'gender' => 'male',
        'birthday' => '2003-08-31',
        'address' => 'Ds. Sumberdawesari',
        'phone' => '082146796695',
        'emergency_phone' => '087849458085'
    ];
    test('success to update patient data', function () use ($dataPatient) {
        $patient = createPatient($dataPatient);
        $token = getToken();

        $dataUpdate = [
            'name' => 'Prawowo Subiawo',
            'address' => 'Timor Leste'
        ];

        $updatePatient = $dataPatient;
        $updatePatient['name'] = $dataUpdate['name'];
        $updatePatient['address'] = $dataUpdate['address'];

        put("/api/patients/{$patient['id']}", $dataUpdate, [
            'Authorization' => 'Bearer ' . $token
        ])->assertOk()->assertJson(successResponse('Berhasil mengupdate data!', $updatePatient));
    });
    test('failed cause nik is exist ', function () use ($dataPatient) {
        $anotherPatientData = $dataPatient;
        $anotherPatientData['nik'] = '3514203108230003';

        $patient = createPatient($dataPatient);
        // create another patient
        createPatient($anotherPatientData);

        $token = getToken();

        $dataUpdate = [
            'nik' => $anotherPatientData['nik'],
            'name' => 'Prawowo Subiawo',
            'address' => 'Timor Leste',
        ];

        $updatePatient = $dataPatient;
        $updatePatient['name'] = $dataUpdate['name'];
        $updatePatient['address'] = $dataUpdate['address'];

        put("/api/patients/{$patient['id']}", $dataUpdate, [
            'Authorization' => 'Bearer ' . $token
        ])->assertConflict()->assertJson(failedResponse('NIK sudah digunakan!', null, 'CONFLICT_ERROR'));
    });
    test('failed cause patient id not found', function () use ($dataPatient) {
        $patient = createPatient($dataPatient);
        $token = getToken();

        $dataUpdate = [
            'name' => 'Prawowo Subiawo',
            'address' => 'Timor Leste'
        ];

        $updatePatient = $dataPatient;
        $updatePatient['name'] = $dataUpdate['name'];
        $updatePatient['address'] = $dataUpdate['address'];

        $patientId = $patient['id'] + 1;

        put("/api/patients/{$patientId}", $dataUpdate, [
            'Authorization' => 'Bearer ' . $token
        ])->assertNotFound()->assertJson(failedResponse('Patient tidak ditemukan!', null, 'NOT_FOUND'));
    });
    test('failed cause token not set', function () use ($dataPatient) {
        $patient = createPatient($dataPatient);
        $token = getToken();

        $dataUpdate = [
            'name' => 'Prawowo Subiawo',
            'address' => 'Timor Leste'
        ];

        $updatePatient = $dataPatient;
        $updatePatient['name'] = $dataUpdate['name'];
        $updatePatient['address'] = $dataUpdate['address'];

        put("/api/patients/{$patient['id']}", $dataUpdate, [
            'Accept' => 'application/json'
        ])
            ->assertUnauthorized()->assertJson(failedResponse('Unauthenticated!', null, 'INVALID_CREDENTIALS'));
    });
    test('failed cause token is invalid', function () use ($dataPatient) {
        $patient = createPatient($dataPatient);
        $token = getToken();

        $dataUpdate = [
            'name' => 'Prawowo Subiawo',
            'address' => 'Timor Leste'
        ];

        $updatePatient = $dataPatient;
        $updatePatient['name'] = $dataUpdate['name'];
        $updatePatient['address'] = $dataUpdate['address'];

        put("/api/patients/{$patient['id']}", $dataUpdate, [
            'Authorization' => 'Bearer salah',
            'Accept' => 'application/json'
        ])
            ->assertUnauthorized()->assertJson(failedResponse('Unauthenticated!', null, 'INVALID_CREDENTIALS'));
    });
});

describe('delete patient', function () {
    $dataPatient = [
        'name' => 'Adolf Hitler',
        'nik' => '3514203108030003',
        'gender' => 'male',
        'birthday' => '2003-08-31',
        'address' => 'Ds. Sumberdawesari',
        'phone' => '082146796695',
        'emergency_phone' => '087849458085'
    ];
    test('success to delete patient data', function () use ($dataPatient) {
        $token = getToken();
        $patient = createPatient($dataPatient);

        delete("/api/patients/{$patient['id']}", headers: [
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json'
        ])->assertOk()->assertJson(successResponse('Berhasil menghapus data!', $dataPatient));
        assertEmpty(getPatient($patient['id']));
    });
    test('not found error cause id not found', function () use ($dataPatient) {
        $token = getToken();
        $patient = createPatient($dataPatient);
        $patientId = $patient['id'] + 1;

        delete("/api/patients/{$patientId}", headers: [
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json'
        ])->assertNotFound()->assertJson(failedResponse('Patient tidak ditemukan!', null, 'NOT_FOUND'));
        assertEquals($patient, getPatient($patient['id']));
    });
    test('unauthorized error cause token not seted', function () use ($dataPatient) {
        $token = getToken();
        $patient = createPatient($dataPatient);

        delete("/api/patients/{$patient['id']}", headers: [
            'Accept' => 'application/json'
        ])->assertUnauthorized()->assertJson(failedResponse('Unauthenticated!', null, 'INVALID_CREDENTIALS'));
        assertEquals($patient, getPatient($patient['id']));
    });
    test('unauthorized error cause token is invalid', function () use ($dataPatient) {
        $token = getToken();
        $patient = createPatient($dataPatient);

        delete("/api/patients/{$patient['id']}", headers: [
            'Authorization' => 'Bearer salah',
            'Accept' => 'application/json'
        ])->assertUnauthorized()->assertJson(failedResponse('Unauthenticated!', null, 'INVALID_CREDENTIALS'));
        assertEquals($patient, getPatient($patient['id']));
    });
});

describe('search patients', function () {
    test('success get patient with name like "dr"', function () {
        $token = getToken();
        $this->seed([PatientSeeder::class]);
        $patients = getPatient(name: 'dr');

        $res = get('/api/patients/search?name=dr', [
            'Authorization' => 'Bearer ' . $token
        ])->assertOk()->json();
        assertEquals(sizeof($patients['data']), sizeof($res['data']['items']));
    });
});

function getPatient($id = null, $name = null, $page = 1, $size = 10): array | null
{
    if (!$name) return Patient::where('id', $id)->first()?->toArray();
    return Patient::where('name', 'like', "%$name%")
        ->paginate($size, page: $page)?->toArray();
}
function createPatient($data): array
{
    $patient = Patient::create($data);
    return $patient->toArray();
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

function successResponse($message, $data)
{
    return [
        'success' => true,
        'data' => $data,
        'errors' => null,
        'message' => $message
    ];
}
function getToken()
{
    $data = [
        'name' => 'test',
        'email' => 'test@gmail.com',
        'phone' => '0822141454',
        'password' => 'testtesttest',
        'role' => 'ADMIN'
    ];
    $user = User::create($data);
    $token = $user->createToken('token_test')->plainTextToken;
    return $token;
}
