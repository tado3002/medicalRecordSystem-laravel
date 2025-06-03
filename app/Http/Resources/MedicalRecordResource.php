<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MedicalRecordResource extends JsonResource
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
            'date' => $this->date,
            'diagnosis' => $this->diagnosis,
            'treatment' => $this->treatment,
            'patient' => new PatientResource($this->patient),
            'docter' => new DocterResource($this->docter)
        ];
    }
}
