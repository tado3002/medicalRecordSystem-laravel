<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class MedicalRecordUpdateRequest extends FormRequest
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
            'docter_id' => 'sometimes|required|integer|min:1',
            'patient_id' => 'sometimes|required|integer|min:1',
            'diagnosis' => 'sometimes|required|string',
            'date' => 'sometimes|required|date',
            'treatment' => 'sometimes|required|string',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => 'User request tidak valid!',
            'data' => null,
            'errors' => [
                'code' => 'INVALID_REQUEST',
                'details' => $validator->getMessageBag()
            ],
        ], 422));
    }
}
