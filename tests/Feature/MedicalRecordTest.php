<?php

use App\Http\Resources\MedicalRecordResource;
use App\Models\Docter;
use App\Models\MedicalRecord;
use App\Models\Patient;
use App\Models\User;
use Database\Seeders\UserSeeder;

use function Pest\Laravel\delete;
use function Pest\Laravel\get;
use function Pest\Laravel\post;
use function Pest\Laravel\put;
use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertNull;

describe('create medical record', function () {
    $medicalRecordData = [
        'docter_id' => [],
        'patient_id' => [],
        'date' => fake()->date(),
        'diagnosis' => fake()->text(20),
        'treatment' => fake()->text(20)
    ];
    test('success', function () use ($medicalRecordData) {
        $this->seed([UserSeeder::class]);
        $token = getToken();

        $docter = createDocter();
        $patient = createPatient();
        $res = post('/api/medical_records', [
            ...$medicalRecordData,
            'docter_id' => $docter['id'],
            'patient_id' => $patient['id']
        ], [
            'Authorization' => "Bearer $token"
        ])->assertCreated()->json();

        $expected = successResponse('Berhasil menambahkan data!', getMedicalRecord($res['data']['id']));
        assertEquals($expected, $res);
    });
    test('not found error cause docter id doesnt exist', function () use ($medicalRecordData) {
        $this->seed([UserSeeder::class]);
        $token = getToken();

        $docter = createDocter();
        $patient = createPatient();
        post('/api/medical_records', [
            ...$medicalRecordData,
            'docter_id' => $docter['id'] + 1,
            'patient_id' => $patient['id']
        ], [
            'Authorization' => "Bearer $token"
        ])->assertNotFound()->assertJson(
            failedResponse(
                'Docter tidak ditemukan!',
                null,
                'NOT_FOUND'
            )
        );
    });
    test('not found error cause patient id doesnt exist', function () use ($medicalRecordData) {
        $this->seed([UserSeeder::class]);
        $token = getToken();

        $docter = createDocter();
        $patient = createPatient();
        post('/api/medical_records', [
            ...$medicalRecordData,
            'docter_id' => $docter['id'],
            'patient_id' => $patient['id'] + 1
        ], [
            'Authorization' => "Bearer $token"
        ])->assertNotFound()->assertJson(
            failedResponse(
                'Patient tidak ditemukan!',
                null,
                'NOT_FOUND'
            )
        );
    });
    test('invalid request error cause user request is not valid', function () use ($medicalRecordData) {
        $this->seed([UserSeeder::class]);
        $token = getToken();

        $docter = createDocter();
        $patient = createPatient();
        post('/api/medical_records', [
            'date' => '01340',
            'diagnosis' => '',
            'treatment' => '',
            'docter_id' => $docter['id'],
            'patient_id' => $patient['id']
        ], [
            'Authorization' => "Bearer $token"
        ])->assertUnprocessable()->assertJson(
            failedResponse('User request tidak valid!', [
                'diagnosis' => ["The diagnosis field is required."],
                'date' => ["The date field must be a valid date."],
                'treatment' => ["The treatment field is required."],

            ], 'REQUEST_INVALID')
        );
    });
    test('unauthorized error cause token is missing', function () use ($medicalRecordData) {
        $this->seed([UserSeeder::class]);
        $token = getToken();

        $docter = createDocter();
        $patient = createPatient();
        post('/api/medical_records', [
            'date' => '01340',
            'diagnosis' => '',
            'treatment' => '',
            'docter_id' => $docter['id'],
            'patient_id' => $patient['id']
        ], [
            'Accept' => "application/json"
        ])->assertUnauthorized()->assertJson(
            failedResponse(
                'Unauthenticated!',
                null,
                'INVALID_CREDENTIALS'
            )
        );
    });
    test('unauthorized error cause token is invalid', function () use ($medicalRecordData) {
        $this->seed([UserSeeder::class]);
        $token = getToken();

        $docter = createDocter();
        $patient = createPatient();
        post('/api/medical_records', [
            'date' => '01340',
            'diagnosis' => '',
            'treatment' => '',
            'docter_id' => $docter['id'],
            'patient_id' => $patient['id']
        ], [
            'Authorization' => "Bearer salah",
            'Accept' => "application/json"
        ])->assertUnauthorized()->assertJson(
            failedResponse(
                'Unauthenticated!',
                null,
                'INVALID_CREDENTIALS'
            )
        );
    });
});

describe('get medical record by id', function () {
    test('success', function () {
        $this->seed([UserSeeder::class]);
        $token = getToken();
        $medicalRecord = createMedicalRecord();
        get('/api/medical_records/' . $medicalRecord['id'], [
            'Authorization' => "Bearer $token",
        ])->assertOk()->assertJson(successResponse('Berhasil mendapatkan data!', $medicalRecord));
    });
    test('not found error cause medical record not exist', function () {
        $this->seed([UserSeeder::class]);
        $token = getToken();
        $medicalRecord = createMedicalRecord();
        get('/api/medical_records/' . $medicalRecord['id'] + 1, [
            'Authorization' => "Bearer $token",
        ])->assertNotFound()->assertJson(failedResponse('Medical record tidak ditemukan!', null, 'NOT_FOUND'));
    });
    test('unauthorized error cause token is missing', function () {
        $this->seed([UserSeeder::class]);
        $token = getToken();

        $medicalRecord = createMedicalRecord();
        get(
            '/api/medical_records/' . $medicalRecord['id'],
            [
                'Accept' => "application/json"
            ]
        )->assertUnauthorized()->assertJson(
            failedResponse(
                'Unauthenticated!',
                null,
                'INVALID_CREDENTIALS'
            )
        );
    });
    test('unauthorized error cause token is invalid', function () {
        $this->seed([UserSeeder::class]);
        $token = getToken();

        $medicalRecord = createMedicalRecord();

        get(
            '/api/medical_records/' . $medicalRecord['id'],
            [
                'Authorization' => "Bearer salah",
                'Accept' => "application/json"
            ]
        )->assertUnauthorized()->assertJson(
            failedResponse(
                'Unauthenticated!',
                null,
                'INVALID_CREDENTIALS'
            )
        );
    });
});

describe('update medical record', function () {
    test('success', function () {
        $this->seed([UserSeeder::class]);
        $token = getToken();

        $docter = createDocter();
        $medicalRecord = createMedicalRecord();

        $res = put('/api/medical_records/' . $medicalRecord['id'], [
            'docter_id' => $docter['id'],
            'diagnosis' => fake()->text(20),
            'treatment' => fake()->text(20)
        ], [
            'Authorization' => "Bearer $token"
        ])->assertOk()->json();

        $expected = successResponse('Berhasil mengupdate data!', getMedicalRecord($medicalRecord['id']));
        assertEquals($expected, $res);
    });
    test('not found error cause docter id doesnt exist', function () {
        $this->seed([UserSeeder::class]);
        $token = getToken();

        $medicalRecord = createMedicalRecord();
        put('/api/medical_records/' . $medicalRecord['id'], [
            'docter_id' => $medicalRecord['docter']['id'] + 1,
        ], [
            'Authorization' => "Bearer $token"
        ])->assertNotFound()->assertJson(
            failedResponse(
                'Docter tidak ditemukan!',
                null,
                'NOT_FOUND'
            )
        );
    });
    test('not found error cause patient id doesnt exist', function () {
        $this->seed([UserSeeder::class]);
        $token = getToken();

        $medicalRecord = createMedicalRecord();
        put('/api/medical_records/' . $medicalRecord['id'], [
            'patient_id' => $medicalRecord['patient']['id'] + 1
        ], [
            'Authorization' => "Bearer $token"
        ])->assertNotFound()->assertJson(
            failedResponse(
                'Patient tidak ditemukan!',
                null,
                'NOT_FOUND'
            )
        );
    });
    test('not found error cause medical record id doesnt exist', function () {
        $this->seed([UserSeeder::class]);
        $token = getToken();

        $medicalRecord = createMedicalRecord();
        $patient = createPatient();
        put('/api/medical_records/' . $medicalRecord['id'] + 1, [
            'patient_id' => $patient['id']
        ], [
            'Authorization' => "Bearer $token"
        ])->assertNotFound()->assertJson(
            failedResponse(
                'Medical record tidak ditemukan!',
                null,
                'NOT_FOUND'
            )
        );
    });
    test('invalid request error cause user request is not valid', function () {
        $this->seed([UserSeeder::class]);
        $token = getToken();

        $docter = createDocter();
        $patient = createPatient();
        $medicalRecord = createMedicalRecord();
        put('/api/medical_records/' . $medicalRecord['id'], [
            'date' => '01340',
            'diagnosis' => '',
            'treatment' => '',
            'docter_id' => $docter['id'],
            'patient_id' => $patient['id']
        ], [
            'Authorization' => "Bearer $token"
        ])->assertUnprocessable()->assertJson(
            failedResponse('User request tidak valid!', [
                'diagnosis' => ["The diagnosis field is required."],
                'date' => ["The date field must be a valid date."],
                'treatment' => ["The treatment field is required."],
            ], 'INVALID_REQUEST')
        );
    });
    test('unauthorized error cause token is missing', function () {
        $this->seed([UserSeeder::class]);
        $token = getToken();

        $medicalRecord = createMedicalRecord();
        put('/api/medical_records/' . $medicalRecord['id'], [
            'date' => '01340',
            'diagnosis' => '',
            'treatment' => '',
        ], [
            'Accept' => "application/json"
        ])->assertUnauthorized()->assertJson(
            failedResponse(
                'Unauthenticated!',
                null,
                'INVALID_CREDENTIALS'
            )
        );
    });
    test('unauthorized error cause token is invalid', function () {
        $this->seed([UserSeeder::class]);
        $token = getToken();

        $medicalRecord = createMedicalRecord();
        put('/api/medical_records/' . $medicalRecord['id'], [
            'date' => '01340',
            'diagnosis' => '',
            'treatment' => '',
        ], [
            'Authorization' => "Bearer salah",
            'Accept' => "application/json"
        ])->assertUnauthorized()->assertJson(
            failedResponse(
                'Unauthenticated!',
                null,
                'INVALID_CREDENTIALS'
            )
        );
    });
});

describe('delete medical record', function () {
    test('success', function () {
        $this->seed([UserSeeder::class]);
        $token = getToken();
        $medicalRecord = createMedicalRecord();
        delete('/api/medical_records/' . $medicalRecord['id'], headers: [
            'Authorization' => "Bearer $token",
        ])->assertOk()->assertJson(successResponse('Berhasil menghapus data!', $medicalRecord));
        assertNull(getMedicalRecord($medicalRecord['id']));
    });
    test('not found error cause medical record not exist', function () {
        $this->seed([UserSeeder::class]);
        $token = getToken();
        $medicalRecord = createMedicalRecord();
        delete('/api/medical_records/' . $medicalRecord['id'] + 1, headers: [
            'Authorization' => "Bearer $token",
        ])->assertNotFound()->assertJson(failedResponse('Medical record tidak ditemukan!', null, 'NOT_FOUND'));
    });
    test('unauthorized error cause token is missing', function () {
        $this->seed([UserSeeder::class]);
        $token = getToken();

        $medicalRecord = createMedicalRecord();
        delete(
            '/api/medical_records/' . $medicalRecord['id'],
            headers: [
                'Accept' => "application/json"
            ]
        )->assertUnauthorized()->assertJson(
            failedResponse(
                'Unauthenticated!',
                null,
                'INVALID_CREDENTIALS'
            )
        );
    });
    test('unauthorized error cause token is invalid', function () {
        $this->seed([UserSeeder::class]);
        $token = getToken();

        $medicalRecord = createMedicalRecord();

        delete(
            '/api/medical_records/' . $medicalRecord['id'],
            headers: [
                'Authorization' => "Bearer salah",
                'Accept' => "application/json"
            ]
        )->assertUnauthorized()->assertJson(
            failedResponse(
                'Unauthenticated!',
                null,
                'INVALID_CREDENTIALS'
            )
        );
    });
})->only();

function createMedicalRecord()
{
    $medicalRecord = MedicalRecord::factory(1)->createOne();
    $medicalRecord = new MedicalRecordResource($medicalRecord);
    return $medicalRecord->toResponse(request())->getData(true)['data'];
}
function failedResponse($message, $details = null, $code)
{
    return [
        'success' => false,
        'message' => $message,
        'data' => null,
        'errors' => [
            'code' => $code,
            'details' => $details
        ]
    ];
}
function successResponse($message, $data)
{
    return [
        'success' => true,
        'message' => $message,
        'data' => $data,
        'errors' => null
    ];
}
function getMedicalRecord($id)
{
    $medicalRecord = MedicalRecord::where('id', $id)->first();
    if (!$medicalRecord) return null;
    $medicalRecord = new MedicalRecordResource($medicalRecord);
    return $medicalRecord->toResponse(request())->getData(true)['data'];
}
function getToken()
{
    $user = User::limit(1)->first();
    return $user->createToken('testing')->plainTextToken;
}

function createDocter()
{
    $docter = Docter::factory(1)->createOne();
    return $docter->toArray();
}

function createPatient()
{
    $patient = Patient::factory(1)->createOne();
    return $patient->toArray();
}
