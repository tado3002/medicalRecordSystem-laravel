<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class PatientUpdateRequest extends FormRequest
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
            'name' => 'sometimes|string|min:6|max:100',
            'nik' => 'sometimes|string',
            'gender' => 'sometimes|in:male,female',
            'birthday' => 'sometimes|date',
            'address' => 'sometimes|min:6|max:100',
            'phone' => 'sometimes|min:6|max:15',
            'emergency_phone' => 'sometimes|min:6|max:15',
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
        ], 400));
    }
}
