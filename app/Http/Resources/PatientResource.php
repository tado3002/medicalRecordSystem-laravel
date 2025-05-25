<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;

class PatientResource extends BaseResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'nik' => $this->nik,
            'gender' => $this->gender,
            'birthday' => $this->birthday,
            'address' => $this->address,
            'phone' => $this->phone,
            'emergency_phone' => $this->emergency_phone
        ];
    }
}
