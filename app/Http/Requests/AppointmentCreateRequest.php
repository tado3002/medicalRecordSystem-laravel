<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class AppointmentCreateRequest extends FormRequest
{
    /**
     * Deter_mine if the user is authorized to make this request.
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
            'docter_id' => 'required',
            'patient_id' => 'required',
            'date' => 'required|date',
            'status' => 'required|in:pending,confirmed,calcelled',
            'notes' => 'required',
        ];
    }
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => 'User request tidak valid!',
            'data' => null,
            'errors' => [
                'code' => 'BAD_REQUEST',
                'details' => $validator->getMessageBag()
            ]
        ], 400));
    }
}
