<?php

namespace App\Http\Controllers;

use App\Models\Docter;
use App\Models\MedicalRecord;
use App\Models\Patient;
use App\Models\Prescription;
use App\Models\User;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Resources\Json\ResourceCollection;

abstract class Controller
{
    public function paginate(ResourceCollection $response)
    {
        return [
            'total' => $response->total(),
            'per_page' => $response->perPage(),
            'current_page' => $response->currentPage(),
            'total_page' => $response->lastPage(),
            'links' => [
                'first' => $response->url(1),
                'last' => $response->url($response->lastPage()),
                'prev' => $response->previousPageUrl(),
                'next' => $response->nextPageUrl(),
            ]
        ];
    }

    public function responseSuccessPaginate(string $message, ResourceCollection $data, int $statusCode = 200)
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => [
                'items' => $data->toArray(request()),
                'page' => $this->paginate($data)
            ],
            'errors' => null
        ], $statusCode);
    }


    public function responseSuccess(string $message, $data, int $statusCode = 200)
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
            'errors' => null
        ], $statusCode);
    }
    public function responseError(string $message, $errorCode = 'UNKNOWN_ERROR', int $statusCode)
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => $message,
            'data' => null,
            'errors' => [
                'code' => $errorCode,
                'details' => null
            ]
        ], $statusCode));
    }
    function throwNotFoundIfUserNotFound(int $id): User
    {
        $user = User::where('id', $id)->first();
        if ($user) return $user;

        $this->responseError('User tidak ditemukan!', 'NOT_FOUND', 404);
    }
    function throwNotFoundIfDocterNotFound(int $id): Docter
    {
        $docter = Docter::where('id', $id)->first();
        if ($docter) return $docter;

        $this->responseError('Docter tidak ditemukan!', 'NOT_FOUND', 404);
    }
    function throwNotFoundIfPatientNotFound(int $id): Patient
    {
        $patient = Patient::where('id', $id)->first();
        if ($patient) return $patient;

        $this->responseError('Patient tidak ditemukan!', 'NOT_FOUND', 404);
    }
    function throwNotFoundIfMedicalRecordNotFound(int $id): MedicalRecord
    {
        $medicalRecord = MedicalRecord::find($id);
        if ($medicalRecord) return $medicalRecord;

        $this->responseError('Medical Record tidak ditemukan!', 'NOT_FOUND', 404);
    }
    function throwNotFoundIfPrescriptionNotFound(int $id): Prescription
    {
        $prescription = Prescription::find($id);
        if ($prescription) return $prescription;

        $this->responseError('Prescription tidak ditemukan!', 'NOT_FOUND', 404);
    }
}
