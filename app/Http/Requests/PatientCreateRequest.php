<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class PatientCreateRequest extends FormRequest
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
            'name' => 'required|string|min:6|max:100',
            'nik' => 'required|string',
            'gender' => 'required|in:male,female',
            'birthday' => 'required|date',
            'address' => 'required|min:6|max:100',
            'phone' => 'required|min:6|max:15',
            'emergency_phone' => 'required|min:6|max:15',
        ];
    }
}
