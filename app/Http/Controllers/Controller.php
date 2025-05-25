<?php

namespace App\Http\Controllers;

use App\Models\Docter;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Http\Exceptions\HttpResponseException;

abstract class Controller
{
    public function paginate($response)
    {
        return [
            'currentPage' => $response->currentPage(),
            'itemPerPage' => $response->perPage(),
            'totalItem' => $response->total(),
            'Links' => $response->links(),
        ];
    }

    public function responseSuccessPaginate(string $message, $data, int $statusCode = 200)
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
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
}
