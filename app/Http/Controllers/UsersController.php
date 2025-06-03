<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserSearchRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class UsersController extends Controller
{
    public function profile(Request $request)
    {
        return $this->responseSuccess(
            'Berhasil mendapatkan data!',
            new UserResource($request->user())
        );
    }
    public function findOne(int $id)
    {
        $user = $this->throwNotFoundIfUserNotFound($id);
        return $this->responseSuccess(
            'Berhasil mendapatkan data!',
            new UserResource($user)
        );
    }
    public function search(UserSearchRequest $request)
    {
        $users = User::where(function (Builder $builder) use ($request) {
            $role = $request['role'] ?? null;
            $name = $request['name'] ?? null;

            if ($role) $builder->where('role', $role);
            if ($name) $builder->where('name', "%$name%");
        })
            ->paginate(perPage: $request['size'], page: $request['page']);


        return $this->responseSuccessPaginate(
            'Berhasil mendapatkan data!',
            UserResource::collection($users)
        );
    }
    public function update(int $id, UserUpdateRequest $userUpdateRequest)
    {
        $data = $userUpdateRequest->validated();
        // cari user, dan throw error jika tidak ada
        $user = $this->throwNotFoundIfUserNotFound($id);
        // filter data tidak boleh empty string
        $user->fill($data);
        $user->save();

        return $this->responseSuccess('Berhasil mengupdate data!', new UserResource($user));
    }
    public function delete(int $id)
    {
        $user = $this->throwNotFoundIfUserNotFound($id);

        $user->delete();
        return $this->responseSuccess(
            'Berhasil menghapus data!',
            new UserResource($user)
        );
    }
}
