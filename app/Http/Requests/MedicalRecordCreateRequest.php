<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

use function Pest\Laravel\json;

class MedicalRecordCreateRequest extends FormRequest
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
            'docter_id' => 'required|integer|min:1',
            'patient_id' => 'required|integer|min:1',
            'diagnosis' => 'required|string',
            'date' => 'required|date',
            'treatment' => 'required|string',
        ];
    }
}
