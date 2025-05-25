<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UserUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() != null;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'sometimes|min:4|max:100',
            'email' => 'sometimes|unique:users|email|max:100',
            'password' => 'sometimes|min:8|max:100',
            'role' => 'sometimes|in:NURSE,ADMIN,DOCTER',
            'phone' => 'sometimes|min:10|max:100'
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response([
            'success' => false,
            'message' => 'User request tidak valid!',
            'errors' => [
                'code' => 'BAD_REQUEST',
                'details' => $validator->getMessageBag()
            ],
            'data' => null
        ], 422));
    }

    protected function failedAuthorization()
    {
        throw new HttpResponseException(response([
            'success' => false,
            'message' => 'Token tidak ditemukan!',
            'errors' => [
                'code' => 'UNAUTHENTICATED!',
                'details' => null
            ],
            'data' => null
        ], 400));
    }
}
