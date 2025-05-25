<?php

namespace App\Http\Controllers;

use App\Http\Requests\PatientCreateRequest;
use App\Http\Requests\PatientUpdateRequest;
use App\Http\Resources\PatientCollection;
use App\Http\Resources\PatientResource;
use App\Models\Patient;
use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Http\Request;

class PatientController extends Controller
{
    public function create(PatientCreateRequest $patientCreateRequest)
    {
        $data = $patientCreateRequest->validated();
        $this->throwConflictIfNikExist($data['nik']);
        $patient = Patient::create($data);

        return $this->responseSuccess(
            'Berhasil menambahkan data!',
            new PatientResource($patient),
            201
        );
    }

    public function findOne(int $id)
    {
        $patient = $this->throwNotFoundIfPatientNotExist($id);
        return $this->responseSuccess(
            'Berhasil mendapatkan data!',
            new PatientResource($patient)
        );
    }

    public function update(int $id, PatientUpdateRequest $patientUpdateRequest)
    {
        $data = $patientUpdateRequest->validated();
        $patient = $this->throwNotFoundIfPatientNotExist($id);
        $this->throwConflictIfNikExist($patientUpdateRequest['nik'], $id);

        $patient->fill($data);
        $patient->save();

        return $this->responseSuccess(
            'Berhasil mengupdate data!',
            new PatientResource($patient),
        );
    }

    public function delete(int $id)
    {
        $patient = $this->throwNotFoundIfPatientNotExist($id);
        $patient->delete();
        return $this->responseSuccess(
            'Berhasil menghapus data!',
            new PatientResource($patient)
        );
    }

    public function search(Request $request)
    {
        $page = $request->input('page', 1);
        $size = $request->input('size', 10);
        $patients = Patient::where(function (Builder $builder) use ($request) {
            $name = $request->input('name');
            if (!empty($name)) $builder->where('name', 'like', "%$name%");
        })->paginate($size, page: $page);

        $patientCollection = new PatientCollection($patients);
        return $this->responseSuccess(
            'Berhasil mendapatkan data!',
            $patientCollection->toArray(request())

        );
    }

    public function throwConflictIfNikExist($nik, int $id = 0)
    {
        // jika id bukan null, maka mencari data berdasarkan kolom nik 
        // dan selain id  
        $patient = Patient::where(function (Builder $builder) use ($nik, $id) {
            if ($id) $builder->where('id', '!=', $id)->get();
            $builder->where('nik', $nik);
        })->first();
        if ($patient) $this->responseError('NIK sudah digunakan!', 'CONFLICT_ERROR', 409);
    }
    public function throwNotFoundIfPatientNotExist($id): Patient
    {
        $patient = Patient::where('id', $id)->first();
        if ($patient) return $patient;
        $this->responseError('Patient tidak ditemukan!', 'NOT_FOUND', 404);
    }
}
