<?php

namespace App\Http\Controllers;

use App\Http\Requests\AppointmentCreateRequest;
use App\Http\Requests\AppointmentSearchRequest;
use App\Http\Requests\AppointmentUpdateRequest;
use App\Http\Resources\AppointmentCollection;
use App\Http\Resources\AppointmentResource;
use App\Models\Appointment;
use App\Models\Docter;
use App\Models\Patient;
use Illuminate\Contracts\Database\Eloquent\Builder;

class AppointmentController extends Controller
{
    public function create(AppointmentCreateRequest $appointmentCreateRequest)
    {
        $data = $appointmentCreateRequest->validated();
        // cari dokter, throw not found jika tidak ada
        $docter = $this->throwNotFoundIfDocterNotExist($data['docter_id']);
        // cari patient, throw not found jika tidak ada
        $patient = $this->throwNotFoundIfPatientNotExist($data['patient_id']);

        $appoinment = new Appointment($data);
        $appoinment->docter_id = $docter->id;
        $appoinment->patient_id = $patient->id;
        $appoinment->save();


        return $this->responseSuccess(
            'Berhasil menambahkan data!',
            new AppointmentResource($appoinment),
            201
        );
    }

    public function update(int $id, AppointmentUpdateRequest $appointmentUpdateRequest)
    {
        $data = $appointmentUpdateRequest->validated();
        $appoinment = $this->throwNotFoundIfAppointmentNotExist($id);
        (!empty($data['docter_id'])) && $this->throwNotFoundIfDocterNotExist($data['docter_id']);
        (!empty($data['patient_id'])) && $this->throwNotFoundIfPatientNotExist($data['patient_id']);

        $appoinment->fill($data);
        $appoinment->save();
        return $this->responseSuccess(
            'Berhasil mengupdate data!',
            new AppointmentResource($appoinment),
            201
        );
    }

    public function delete(int $id)
    {
        $appointment = $this->throwNotFoundIfAppointmentNotExist($id);
        $appointment->delete();
        return $this->responseSuccess('Berhasil menghapus data!', [
            new AppointmentResource($appointment),
        ]);
    }

    public function search(AppointmentSearchRequest $appointmentSearchRequest)
    {
        $request = $appointmentSearchRequest->validated();

        $appointments = Appointment::where(function (Builder $builder) use ($request) {
            $docterName = $request['docter_name'] ?? null;
            // inner join ke tabel dokter
            if ($docterName)
                $builder->whereHas('docter', function ($docterQuery) use ($docterName) {
                    // inner join ke tabel user
                    $docterQuery->whereHas('user', function ($userQuery) use ($docterName) {
                        // where user.name like
                        $userQuery->where('name', 'like', "%$docterName%");
                    });
                });

            $patientName = $request['patient_name'] ?? null;
            // inner join ke tabel patient
            if ($patientName)
                $builder->whereHas('patient', function ($patientQuery) use ($patientName) {
                    // where patient.name like
                    $patientQuery->where('name', 'like', "%$patientName%");
                });

            $status = $request['status'] ?? null;
            // status filter
            if ($status) $builder->where('status', $status);
        })
            ->paginate($request['size'], page: $request['page']);

        $appoinments = new AppointmentCollection($appointments);

        return $this->responseSuccessPaginate(
            'Berhasil mendapatkan data!',
            $appoinments->toArray(request())
        );
    }

    public function throwNotFoundIfDocterNotExist($id): Docter
    {
        $docter = Docter::where('id', $id)->first();
        if ($docter) return $docter;
        $this->responseError(
            'Dokter tidak ditemukan!',
            'NOT_FOUND',
            404
        );
    }
    public function throwNotFoundIfPatientNotExist($id): Patient
    {
        $patient = Patient::where('id', $id)->first();
        if ($patient) return $patient;
        $this->responseError(
            'Pasien tidak ditemukan!',
            'NOT_FOUND',
            404
        );
    }
    public function throwNotFoundIfAppointmentNotExist($id): Appointment
    {
        $appointment = Appointment::where('id', $id)->first();
        if ($appointment) return $appointment;
        $this->responseError(
            'Appointment tidak ditemukan!',
            'NOT_FOUND',
            404
        );
    }
}
