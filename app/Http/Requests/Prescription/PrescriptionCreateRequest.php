<?php

namespace App\Http\Requests\Prescription;

use Illuminate\Foundation\Http\FormRequest;

class PrescriptionCreateRequest extends FormRequest
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
            'medical_record_id' => ['required', 'integer', 'min:1'],
            'medicine_name' => ['required', 'string', 'min:3', 'max:100'],
            'dosage' => ['required', 'string', 'min:3', 'max:100'],
            'frequency' => ['required', 'string', 'min:3', 'max:100'],
            'notes' => ['required', 'string', 'min:3', 'max:255'],
        ];
    }
}
