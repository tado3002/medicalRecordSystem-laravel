<?php

use App\Http\Resources\AppointmentCollection;
use App\Http\Resources\AppointmentResource;
use App\Models\Appointment;
use App\Models\Docter;
use App\Models\Patient;
use App\Models\User;
use Database\Seeders\AppointmentCollectionSeeder;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;

use function Pest\Laravel\delete;
use function Pest\Laravel\get;
use function Pest\Laravel\post;
use function Pest\Laravel\put;
use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertNotNull;
use function PHPUnit\Framework\assertNull;

describe('create appointment test', function () {
    $dataAppoinment = [
        'status' => 'pending',
        'date' => '2025-05-28',
        'notes' => 'note1'
    ];
    test('success create appointment', function () use ($dataAppoinment) {
        $patient = createPatient();
        $docter = createDocter();
        $token = getToken();

        $createAppointment = $dataAppoinment;
        $createAppointment['docter_id'] = $docter->id;
        $createAppointment['patient_id'] = $patient->id;

        post('/api/appointments', $createAppointment, [
            'Authorization' => 'Bearer ' . $token
        ])->assertCreated()->assertJson(successResponse('Berhasil menambahkan data!', $dataAppoinment));
    });
    test('not found error cause docter id not exist ', function () use ($dataAppoinment) {
        $patient = createPatient();
        $docter = createDocter();
        $token = getToken();

        $createAppointment = $dataAppoinment;
        $createAppointment['docter_id'] = $docter->id + 1;
        $createAppointment['patient_id'] = $patient->id;

        post('/api/appointments', $createAppointment, [
            'Authorization' => 'Bearer ' . $token
        ])->assertNotFound()->assertJson(failedResponse('Dokter tidak ditemukan!', null, 'NOT_FOUND'));
    });
    test('not found error cause patient id not exist ', function () use ($dataAppoinment) {
        $patient = createPatient();
        $docter = createDocter();
        $token = getToken();

        $createAppointment = $dataAppoinment;
        $createAppointment['docter_id'] = $docter->id;
        $createAppointment['patient_id'] = $patient->id + 1;

        post('/api/appointments', $createAppointment, [
            'Authorization' => 'Bearer ' . $token
        ])->assertNotFound()->assertJson(failedResponse('Pasien tidak ditemukan!', null, 'NOT_FOUND'));
    });
    test('bad request error cause user request is invalid ', function () use ($dataAppoinment) {
        $patient = createPatient();
        $docter = createDocter();
        $token = getToken();

        $createAppointment = $dataAppoinment;

        $createAppointment['notes'] = '';
        $createAppointment['status'] = 'wudehel';
        $createAppointment['docter_id'] = $docter->id;
        $createAppointment['patient_id'] = $patient->id;

        post('/api/appointments', $createAppointment, [
            'Authorization' => 'Bearer ' . $token
        ])->assertBadRequest()->assertJson(failedResponse('User request tidak valid!', [
            'status' => ['The selected status is invalid.'],
            'notes' => ['The notes field is required.']

        ], 'BAD_REQUEST'));
    });
    test('unauthenticated error cause token not set', function () use ($dataAppoinment) {
        $patient = createPatient();
        $docter = createDocter();
        $token = getToken();

        $createAppointment = $dataAppoinment;
        $createAppointment['docter_id'] = $docter->id;
        $createAppointment['patient_id'] = $patient->id;

        post('/api/appointments', $createAppointment, [
            'Accept' => 'application/json '
        ])->assertUnauthorized()->assertJson(failedResponse('Unauthenticated!', null, 'INVALID_CREDENTIALS'));
    });
    test('unauthenticated error cause token is invalid', function () use ($dataAppoinment) {
        $patient = createPatient();
        $docter = createDocter();
        $token = getToken();

        $createAppointment = $dataAppoinment;
        $createAppointment['docter_id'] = $docter->id;
        $createAppointment['patient_id'] = $patient->id;

        post('/api/appointments', $createAppointment, [
            'Authorization' => 'Bearer salah',
            'Accept' => 'application/json '
        ])->assertUnauthorized()->assertJson(failedResponse('Unauthenticated!', null, 'INVALID_CREDENTIALS'));
    });
});
describe('update appointment test', function () {
    $dataAppoinment = [
        'status' => 'pending',
        'date' => '2025-05-28',
        'notes' => 'note1'
    ];
    test('success update appointment', function () use ($dataAppoinment) {
        $patient = createPatient();
        $patientAnother = createPatient();
        $docter = createDocter();
        $docterAnother = createDocter();
        $token = getToken();

        $createAppointment = [
            ...$dataAppoinment,
            'docter_id' => $docter->id,
            'patient_id' => $patient->id
        ];

        $createdAppointment = createAppointment($createAppointment);

        $updateAppointment = [
            ...$createAppointment,
            'docter_id' => $docterAnother->id,
            'patient_id' => $patientAnother->id,
        ];

        put('/api/appointments/' . $createdAppointment->id, $updateAppointment, [
            'Authorization' => 'Bearer ' . $token
        ])->assertCreated()->assertJson(successResponse('Berhasil mengupdate data!', $updateAppointment));
    });
    test('not found error cause docter_id not found', function () use ($dataAppoinment) {
        $patient = createPatient();
        $patientAnother = createPatient();
        $docter = createDocter();
        $docterAnother = createDocter();
        $token = getToken();

        $createAppointment = [
            ...$dataAppoinment,
            'docter_id' => $docter->id,
            'patient_id' => $patient->id
        ];

        $createdAppointment = createAppointment($createAppointment);

        $updateAppointment = [
            ...$createAppointment,
            'docter_id' => $docterAnother->id + 1,
            'patient_id' => $patientAnother->id,
        ];

        put('/api/appointments/' . $createdAppointment->id, $updateAppointment, [
            'Authorization' => 'Bearer ' . $token
        ])->assertNotFound()->assertJson(failedResponse('Dokter tidak ditemukan!', null, 'NOT_FOUND'));
    });
    test('not found error cause patient_id not found', function () use ($dataAppoinment) {
        $patient = createPatient();
        $patientAnother = createPatient();
        $docter = createDocter();
        $docterAnother = createDocter();
        $token = getToken();

        $createAppointment = [
            ...$dataAppoinment,
            'docter_id' => $docter->id,
            'patient_id' => $patient->id
        ];

        $createdAppointment = createAppointment($createAppointment);

        $updateAppointment = [
            ...$createAppointment,
            'docter_id' => $docterAnother->id,
            'patient_id' => $patientAnother->id + 1,
        ];

        put('/api/appointments/' . $createdAppointment->id, $updateAppointment, [
            'Authorization' => 'Bearer ' . $token
        ])->assertNotFound()->assertJson(failedResponse('Pasien tidak ditemukan!', null, 'NOT_FOUND'));
    });
    test('not found error cause appointment_id not found', function () use ($dataAppoinment) {
        $patient = createPatient();
        $patientAnother = createPatient();
        $docter = createDocter();
        $docterAnother = createDocter();
        $token = getToken();

        $createAppointment = [
            ...$dataAppoinment,
            'docter_id' => $docter->id,
            'patient_id' => $patient->id
        ];

        $createdAppointment = createAppointment($createAppointment);

        $updateAppointment = [
            ...$createAppointment,
            'docter_id' => $docterAnother->id,
            'patient_id' => $patientAnother->id,
        ];

        put('/api/appointments/' . $createdAppointment->id + 1, $updateAppointment, [
            'Authorization' => 'Bearer ' . $token
        ])->assertNotFound()->assertJson(failedResponse('Appointment tidak ditemukan!', null, 'NOT_FOUND'));
    });
    test('bad request error cause user request not valid', function () use ($dataAppoinment) {
        $patient = createPatient();
        $patientAnother = createPatient();
        $docter = createDocter();
        $docterAnother = createDocter();
        $token = getToken();

        $createAppointment = [
            ...$dataAppoinment,
            'docter_id' => $docter->id,
            'patient_id' => $patient->id
        ];

        $createdAppointment = createAppointment($createAppointment);

        $updateAppointment = [
            ...$createAppointment,
            'docter_id' => $docterAnother->id,
            'patient_id' => $patientAnother->id,
            'notes' => ''
        ];

        put('/api/appointments/' . $createdAppointment->id, $updateAppointment, [
            'Authorization' => 'Bearer ' . $token
        ])->assertBadRequest()->assertJson(failedResponse('User request tidak valid!', [
            'notes' => ["The notes field is required."]
        ], 'BAD_REQUEST'));
    });
    test('unauthorized error cause token not set', function () use ($dataAppoinment) {
        $patient = createPatient();
        $patientAnother = createPatient();
        $docter = createDocter();
        $docterAnother = createDocter();
        $token = getToken();

        $createAppointment = [
            ...$dataAppoinment,
            'docter_id' => $docter->id,
            'patient_id' => $patient->id
        ];

        $createdAppointment = createAppointment($createAppointment);

        $updateAppointment = [
            ...$createAppointment,
            'docter_id' => $docterAnother->id,
            'patient_id' => $patientAnother->id,
        ];

        put('/api/appointments/' . $createdAppointment->id, $updateAppointment, [
            'Accept' => 'application/json'
        ])->assertUnauthorized()->assertJson(failedResponse('Unauthenticated!', null, 'INVALID_CREDENTIALS'));
    });
    test('unauthorized error cause token is invalid', function () use ($dataAppoinment) {
        $patient = createPatient();
        $patientAnother = createPatient();
        $docter = createDocter();
        $docterAnother = createDocter();
        $token = getToken();

        $createAppointment = [
            ...$dataAppoinment,
            'docter_id' => $docter->id,
            'patient_id' => $patient->id
        ];

        $createdAppointment = createAppointment($createAppointment);

        $updateAppointment = [
            ...$createAppointment,
            'docter_id' => $docterAnother->id,
            'patient_id' => $patientAnother->id,
        ];

        put('/api/appointments/' . $createdAppointment->id, $updateAppointment, [
            'Authorization' => 'Bearer salah',
            'Accept' => 'application/json'
        ])->assertUnauthorized()->assertJson(failedResponse('Unauthenticated!', null, 'INVALID_CREDENTIALS'));
    });
});
describe('delete appointment test', function () {
    $dataAppoinment = [
        'status' => 'pending',
        'date' => '2025-05-28',
        'notes' => 'note1'
    ];
    test('success delete appointment', function () use ($dataAppoinment) {
        $token = getToken();
        $docter = createDocter();
        $patient = createPatient();
        $dataAppoinment['docter_id'] = $docter->id;
        $dataAppoinment['patient_id'] = $patient->id;
        $appointment = createAppointment($dataAppoinment);

        delete('/api/appointments/' . $appointment->id, headers: [
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json'
        ])->assertOk()->assertJson(
            successResponse(
                'Berhasil menghapus data!',
                (new AppointmentResource($appointment))->with
            )
        );

        assertNull(getAppointment($appointment->id));
    });
    test('errror not found cause appointment_id not found', function () use ($dataAppoinment) {
        $token = getToken();
        $docter = createDocter();
        $patient = createPatient();
        $dataAppoinment['docter_id'] = $docter->id;
        $dataAppoinment['patient_id'] = $patient->id;
        $appointment = createAppointment($dataAppoinment);

        delete('/api/appointments/' . $appointment->id + 1, headers: [
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json'
        ])->assertNotFound()->assertJson(
            failedResponse('Appointment tidak ditemukan!', null, 'NOT_FOUND')
        );

        assertNotNull(getAppointment($appointment->id));
    });
    test('unauthorized error cause token not set', function () use ($dataAppoinment) {
        $token = getToken();
        $docter = createDocter();
        $patient = createPatient();
        $dataAppoinment['docter_id'] = $docter->id;
        $dataAppoinment['patient_id'] = $patient->id;
        $appointment = createAppointment($dataAppoinment);

        delete('/api/appointments/' . $appointment->id, headers: [
            'Accept' => 'application/json'
        ])->assertUnauthorized()->assertJson(
            failedResponse('Unauthenticated!', null, 'INVALID_CREDENTIALS')
        );

        assertNotNull(getAppointment($appointment->id));
    });
    test('unauthorized error cause token is invalid', function () use ($dataAppoinment) {
        $token = getToken();
        $docter = createDocter();
        $patient = createPatient();
        $dataAppoinment['docter_id'] = $docter->id;
        $dataAppoinment['patient_id'] = $patient->id;
        $appointment = createAppointment($dataAppoinment);

        delete('/api/appointments/' . $appointment->id, headers: [
            'Authorization' => 'Bearer ' . $token . '1',
            'Accept' => 'application/json'
        ])->assertUnauthorized()->assertJson(
            failedResponse('Unauthenticated!', null, 'INVALID_CREDENTIALS')
        );

        assertNotNull(getAppointment($appointment->id));
    });
});
describe('search appointments paginate by docters name or patients name', function () {
    test('success to get appointment by ignore parameter', function () {
        $this->seed([AppointmentCollectionSeeder::class]);
        $token = getToken();
        $appointments = getAppointmentsPaginate();
        $res = get('api/appointments/search', [
            'Authorization' => 'Bearer ' . $token
        ])->assertOk()->json();
        $expected = successResponsePaginated('Berhasil mendapatkan data!', $appointments)->getData(true);
        assertEquals(
            $expected['data']['items'],
            $res['data']['items']
        );
    });
    test('success to get appointment by parameter page = 2 and size = 10', function () {
        $token = getToken();
        $this->seed([AppointmentCollectionSeeder::class]);
        $appointments = getAppointmentsPaginate(2, 10);
        $res = get('api/appointments/search?page=2&&size=10', [
            'Authorization' => 'Bearer ' . $token
        ])->assertOk()->json();
        $expected = successResponsePaginated('Berhasil mendapatkan data!', $appointments)->getData(true);
        assertEquals(
            $expected['data']['items'],
            $res['data']['items']
        );
    });
    test('success to get appointment by parameter docter name', function () {
        $this->seed([AppointmentCollectionSeeder::class]);
        $docterName = 'hoeger';
        $appointments = getAppointmentsPaginate(2, 10, [
            'docter_name' => $docterName
        ]);
        $token = getToken();
        $res = get("api/appointments/search?page=2&&size=10&&docter_name={$docterName}", [
            'Authorization' => 'Bearer ' . $token
        ])->assertOk()->json();
        $expected = successResponsePaginated('Berhasil mendapatkan data!', $appointments)->getData(true);
        assertEquals(
            $expected['data']['items'],
            $res['data']['items']
        );
    });
    test('success to get appointment by parameter patient name', function () {
        $this->seed([AppointmentCollectionSeeder::class]);
        $patientName = 'jeralds';
        $appointments = getAppointmentsPaginate(param: [
            'patient_name' => $patientName
        ]);
        $token = getToken();
        $res = get("api/appointments/search?patient_name={$patientName}", [
            'Authorization' => 'Bearer ' . $token
        ])->assertOk()->json();
        $expected = successResponsePaginated('Berhasil mendapatkan data!', $appointments)->getData(true);
        assertEquals(
            $expected['data']['items'],
            $res['data']['items']
        );
    });
    test('success to get appointment by parameter status pending', function () {
        $this->seed([AppointmentCollectionSeeder::class]);
        $status = 'pending';
        $appointments = getAppointmentsPaginate(param: [
            'status' => $status
        ]);
        $token = getToken();
        $res = get("api/appointments/search?status={$status}", [
            'Authorization' => 'Bearer ' . $token
        ])->assertOk()->json();
        $expected = successResponsePaginated('Berhasil mendapatkan data!', $appointments)->getData(true);
        assertEquals(
            $expected['data']['items'],
            $res['data']['items']
        );
    });
});

function getAppointmentsPaginate($page = 1, $size = 10, $param = []): AppointmentCollection
{
    $appointments = Appointment::where(function (Builder $builder) use ($param) {
        $docterName = $param['docter_name'] ??  null;
        $patientName = $param['patient_name'] ?? null;
        $status = $param['status'] ?? null;

        // docter name filter
        if ($docterName)  $builder->whereHas('docter', function ($query) use ($docterName) {
            $query->whereHas('user', function ($userQuery) use ($docterName) {
                $userQuery->where('name', 'like', "%$docterName%");
            });
        });
        // patient name filter
        if ($patientName) $builder->whereHas('patient', function ($query) use ($patientName) {
            $query->where('name', 'like', "%$patientName%");
        });
        // status filter
        $status && $builder->where('status', $status);
    })
        ->paginate($size, page: $page);

    return new AppointmentCollection($appointments);
}
function getAppointment($id): Appointment | null
{
    return Appointment::where('id', $id)->first();
}
function createAppointment($data): Appointment
{
    return Appointment::create($data);
}
function getPatient($id = null, $name = null, $page = 1, $size = 10): array | null
{
    if (!$name) return Patient::where('id', $id)->first()?->toArray();
    return Patient::where('name', 'like', "%$name%")
        ->paginate($size, page: $page)?->toArray();
}

function createDocter($data = null): Docter
{
    // data default
    $data = !$data ? [
        'name' => fake()->name(),
        'email' => fake()->email(),
        'phone' => fake()->phoneNumber(),
        'password' => fake()->text(10),
        'role' => 'DOCTER'
    ] : $data;

    $user = createUser($data);
    return Docter::create([
        'specialization' => 'Dokter mata',
        'user_id' => $user->id
    ]);
}

function createPatient($data = null): Patient
{
    $data = !$data ? [
        'name' => fake()->name(),
        'nik' => '351420310803000' . fake()->randomNumber(),
        'gender' => fake()->randomElement(['male', 'female']),
        'birthday' => fake()->date(),
        'address' => fake()->address(),
        'phone' => fake()->phoneNumber(),
        'emergency_phone' => fake()->phoneNumber()
    ] : $data;

    $patient = Patient::create($data);
    return $patient;
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

function successResponsePaginated($message, $data, $baseUrl = null): JsonResponse
{
    $baseUrl && $baseUrl->fragment($baseUrl);
    $res = successResponse($message, $data->toArray(request()));
    return response()->json($res);
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
function createUser($data): User
{
    return User::create($data);
}
function getToken()
{
    $data = [
        'name' => 'test',
        'email' => 'test@gmail.com',
        'phone' => '0822141454',
        'password' => 'testtesttest',
        'role' => 'DOCTER'
    ];
    $user = createUser($data);
    $token = $user->createToken('token_test')->plainTextToken;
    return $token;
}
