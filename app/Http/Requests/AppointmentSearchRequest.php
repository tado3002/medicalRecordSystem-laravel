<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AppointmentSearchRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'page' => $this->page ?? 1,
            'size' => $this->size ?? 10
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'page' => 'sometimes|integer|min:1',
            'size' => 'sometimes|integer|min:5|max:30',
            'status' => 'sometimes|in:pending,cancelled,confirmed',
            'docter_name' => 'sometimes|string|min:5|max:20',
            'patient_name' => 'sometimes|string|min:5|max:20',
        ];
    }
}
