<?php

namespace App\Http\Controllers;

use App\Http\Requests\MedicalRecordCreateRequest;
use App\Http\Requests\MedicalRecordUpdateRequest;
use App\Http\Resources\MedicalRecordResource;
use App\Models\MedicalRecord;

class MedicalRecordController extends Controller
{
    public function create(MedicalRecordCreateRequest $medicalRecordCreateRequest)
    {
        $data = $medicalRecordCreateRequest->validated();
        $this->throwNotFoundIfDocterNotFound($data['docter_id']);
        $this->throwNotFoundIfPatientNotFound($data['patient_id']);

        $medicalRecord = MedicalRecord::create($data);
        return $this->responseSuccess(
            'Berhasil menambahkan data!',
            new MedicalRecordResource($medicalRecord),
            201
        );
    }

    public function findOne(int $id)
    {
        $medicalRecord = $this->throwNotFoundIfMedicalRecordNotExist($id);

        return $this->responseSuccess(
            'Berhasil mendapatkan data!',
            new MedicalRecordResource($medicalRecord)
        );
    }

    public function update(int $id, MedicalRecordUpdateRequest $medicalRecordCreateRequest)
    {
        $data = $medicalRecordCreateRequest->validated();

        !empty($data['docter_id']) && $this->throwNotFoundIfDocterNotFound($data['docter_id']);
        !empty($data['patient_id']) && $this->throwNotFoundIfPatientNotFound($data['patient_id']);

        $medicalRecord = $this->throwNotFoundIfMedicalRecordNotExist($id);
        $medicalRecord->fill($data);
        $medicalRecord->save();

        return $this->responseSuccess(
            'Berhasil mengupdate data!',
            new MedicalRecordResource($medicalRecord)
        );
    }

    public function delete(int $id)
    {
        $medicalRecord = $this->throwNotFoundIfMedicalRecordNotExist($id);

        $medicalRecord->delete();
        return $this->responseSuccess(
            'Berhasil menghapus data!',
            new MedicalRecordResource($medicalRecord)
        );
    }

    public function throwNotFoundIfMedicalRecordNotExist(int $id)
    {
        $medicalRecord = MedicalRecord::where('id', $id)->first();
        if ($medicalRecord) return $medicalRecord;
        $this->responseError('Medical record tidak ditemukan!', 'NOT_FOUND', 404);
    }
}
