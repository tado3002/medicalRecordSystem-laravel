<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AppointmentResource extends JsonResource
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
            'docter_id' => $this->docter_id,
            'patient_id' => $this->patient_id,
            'status' => $this->status,
            'date' => $this->date,
            'notes' => $this->notes,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'patient' => new PatientResource($this->patient),
            'docter' => new DocterResource($this->docter),
        ];
    }
}
