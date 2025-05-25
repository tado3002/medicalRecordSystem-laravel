<?php

namespace App\Http\Controllers;

use App\Http\Requests\DocterRequest;
use App\Http\Requests\DocterUpdateRequest;
use App\Http\Resources\DocterCollection;
use App\Http\Resources\DocterResource;
use App\Models\Docter;
use App\Models\User;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class DocterController extends Controller
{
    public function create(DocterRequest $docterRequest)
    {
        $data = $docterRequest->validated();
        $user = $this->throwNotFoundIfUserNotFound($data['user_id']);

        $this->throwForbiddenIfRoleNotDocter($user);

        $docter = new Docter([
            'user_id' => $user->id,
            'specialization' => $data['specialization']
        ]);
        $docter->save();

        return $this->responseSuccess(
            'Berhasil menambahkan data!',
            new DocterResource($docter),
            201
        );
    }

    public function findOne(int $id)
    {
        $docter = $this->throwNotFoundIfDocterNotExist($id);
        return $this->responseSuccess(
            'Berhasil mendapatkan data!',
            new DocterResource($docter)
        );
    }

    public function findAll()
    {
        $docters = Docter::all();
        $docterCollection = new DocterCollection($docters);
        return $this->responseSuccess(
            'Berhasil mendapatkan data!',
            $docterCollection
        );
    }

    public function update(int $id, DocterUpdateRequest $docterUpdateRequest)
    {
        $data = $docterUpdateRequest->validated();

        $docter = $this->throwNotFoundIfDocterNotExist($id);
        $docter->specialization = $data['specialization'];
        $docter->save();

        return $this->responseSuccess(
            'Berhasil mengupdate data!',
            new DocterResource($docter)
        );
    }

    public function delete(int $id)
    {
        $docter = $this->throwNotFoundIfDocterNotExist($id);
        $docter->delete();
        return $this->responseSuccess(
            'Berhasil menghapus data!',
            new DocterResource($docter)
        );
    }

    public function search(Request $request)
    {
        $page = $request->input('page', 1);
        $size = $request->input('size', 10);
        $docters = Docter::where(function (Builder $builder) use ($request) {
            $specialization = $request->input('specialization');
            $name = $request->input('name');
            if (!empty($specialization)) $builder->where('specialization', 'like', "%$specialization%");
            if (!empty($name)) $builder->whereHas('user', function ($query) use ($name) {
                $query->where('name', 'like', "%$name%");
            });
        });

        $docters = $docters
            ->paginate($size, page: $page);

        $docterCollection =  new DocterCollection($docters);
        return $this->responseSuccess(
            'Berhasil mendapatkan data!',
            $docterCollection->toArray(request())
        );
    }


    public function throwNotFoundIfDocterNotExist($id)
    {
        $docter = Docter::where('id', $id)->first();

        if ($docter) return $docter;
        $this->responseError('Spesialisasi dokter tidak ditemukan!', 'NOT_FOUND', 404);
    }

    public function throwForbiddenIfRoleNotDocter(User $user)
    {
        if ($user->role == 'DOCTER') return;
        $this->responseError('Id tidak terdaftar sebagai DOCTER!', 'FORBIDDEN', 403);
    }
}
