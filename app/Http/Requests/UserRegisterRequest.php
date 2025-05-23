<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UserRegisterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|min:4|max:100',
            'email' => 'required|max:100|email',
            'password' => 'required|min:8|max:100',
            'role' => ['required', 'in:NURSE,ADMIN,DOCTER'],
            'phone' => 'required|min:10|max:100'
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response([
            'success' => false,
            'message' => 'User request tidak valid!',
            'errors' => [
                'code' => 'BAD_REQUEST',
                'details' => $validator->getMessageBag(),
            ],
            'data' => null
        ], 400));
    }
}
