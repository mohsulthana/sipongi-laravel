<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\Handler as Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\Users\ListResource;
use App\Http\Resources\Users\ListResources;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Activitylog\ActivityLogger;

class UsersController extends Controller
{
    public function getDt()
    {
        $per_page = !request()->filled('per_page') ? 10 : (int) request('per_page');
        $direction = request()->query('direction');
        $sortBy = request()->query('sortBy');

        $query = User::query()->with(['roles', 'regional', 'provinsi'])->select(['users.*']);
        $query->leftJoin(config('permission.table_names.model_has_roles') . ' as hr', function ($join) {
            $join->on('hr.' . config('permission.column_names.model_morph_key'), '=', 'users.id');
        });
        $query->leftJoin(config('permission.table_names.roles') . ' as r', function ($join) {
            $join->on('r.id', '=', 'hr.role_id');
        });
        $query->leftJoin('regional as reg', function ($join) {
            $join->on('reg.id', '=', 'users.regional_id');
        });
        $query->leftJoin('provinsi as pr', function ($join) {
            $join->on('pr.id', '=', 'users.provinsi_id');
        });

        $query->when(request()->filled('name'), function ($query) {
            return $query->where('users.name', 'ILIKE', "%" . request()->query('name') . "%");
        });
        $query->when(request()->filled('username'), function ($query) {
            return $query->where('users.username', 'ILIKE', "%" . request()->query('username') . "%");
        });
        $query->when(request()->filled('role'), function ($query) {
            if (request()->query('role') == 'superAdmin') {
                return $query->where('users.is_super_admin', true);
            } else {
                return $query->where('r.name', request()->query('role'));
            }
        });
        $query->when(request()->filled('status'), function ($query) {
            return $query->where('users.status', request()->query('status'));
        });
        $query->when(request()->filled('email'), function ($query) {
            return $query->where('users.email', 'LIKE', "%" . request()->query('email') . "%");
        });
        $query->when(request()->filled('regional_id'), function ($query) {
            return $query->where('users.regional_id', request()->query('regional_id'));
        });
        $query->when(request()->filled('provinsi_id'), function ($query) {
            return $query->where('users.provinsi_id', request()->query('provinsi_id'));
        });

        $query->when(request()->filled('deleted'), function ($query) {
            if (request()->query('deleted') === '1') {
                return $query->withTrashed();
            }

            if (request()->query('deleted') === '2') {
                return $query->onlyTrashed();
            }

            return $query;
        });

        $query->where('username', '<>', 'SysAdminSipongi');

        $users = $query->orderBy($sortBy, $direction)->paginate($per_page);

        return new ListResources($users);
    }

    public function store(Request $request)
    {
        $rules = [
            'username' => 'required|alpha_dash|unique:users',
            'email' => 'nullable|string|email',
            'name' => 'required|string',
            'password' => 'required|string|strong_password',
            'role' => 'required_if:is_super_admin,false|string|nullable',
            'status' => 'required|boolean'
        ];

        $messages = [
            'username.required' => __('validation.required', ['attribute' => "Nama Pengguna"]),
            'username.alpha_dash' => __('validation.alpha_dash', ['attribute' => "Nama Pengguna"]),
            'email.email' => __('validation.email', ['attribute' => 'Email']),
            'name.required' => __('validation.required', ['attribute' => 'Nama Lengkap']),
            'password.required' => __('validation.required', ['attribute' => 'Kata Sandi']),
            'password.string' => __('validation.string', ['attribute' => 'Kata Sandi']),
            'password.strong_password' => __('validation.strong_password', ['attribute' => 'Kata Sandi']),
            'role.required_if' => __('validation.required', ['attribute' => 'Hak Akses']),
            'role.string' => __('validation.string', ['attribute' => 'Hak Akses']),
            'status.required' => __('validation.required', ['attribute' => 'Status']),
            'status.boolean' => __('validation.boolean', ['attribute' => 'Status']),
        ];

        $this->validate($request, $rules, $messages);

        DB::beginTransaction();

        try {
            $user = new User;
            $user->name = $request->get('name');
            $user->email = $request->get('email');
            $user->regional_id = $request->get('regional_id');
            $user->provinsi_id = $request->get('provinsi_id');
            $user->provinsi_id = $request->get('provinsi_id');
            $user->username = $request->get('username');
            $user->is_super_admin = $request->get('is_super_admin');
            $user->status = $request->get('status');
            $user->password = Hash::make($request->get('password'));
            $user->email_verified_at = Carbon::now();
            $user->tmp_role = $request->get('role');

            $user->save();
            $user->refresh();
            $user->syncRoles([$request->get('role')]);
        } catch (Exception $e) {
            DB::rollback();
            return response()->json(array(
                'status' => false,
                'message' => __('exception.handler.500'),
                'errors' => $e
            ), 500);
        }

        DB::commit();
        return response()->json(array('status' => true), 200);
    }

    public function edit($id)
    {
        $user = User::query()
            ->with(['roles', 'regional', 'provinsi'])
            ->where('id', $id)
            ->where('username', '<>', 'SysAdminSipongi')
            ->withTrashed()
            ->firstOrFail();
        ListResource::withoutWrapping();

        return new ListResource($user);
    }

    public function update(Request $request, $id)
    {
        $rules = [
            'username' => "required|alpha_dash|unique:users,username,$id",
            'email' => 'nullable|string|email',
            'name' => 'required|string',
            'password' => 'nullable|string|strong_password',
            'role' => 'required_if:is_super_admin,false|string|nullable',
            'status' => 'required|boolean'
        ];

        $messages = [
            'username.required' => __('validation.required', ['attribute' => "Nama Pengguna"]),
            'username.alpha_dash' => __('validation.alpha_dash', ['attribute' => "Nama Pengguna"]),
            'email.email' => __('validation.email', ['attribute' => 'Email']),
            'name.required' => __('validation.required', ['attribute' => 'Nama Lengkap']),
            'password.string' => __('validation.string', ['attribute' => 'Kata Sandi']),
            'password.strong_password' => __('validation.strong_password', ['attribute' => 'Kata Sandi']),
            'role.required_if' => __('validation.required', ['attribute' => 'Hak Akses']),
            'role.string' => __('validation.string', ['attribute' => 'Hak Akses']),
            'status.required' => __('validation.required', ['attribute' => 'Status']),
            'status.boolean' => __('validation.boolean', ['attribute' => 'Status']),
        ];

        $this->validate($request, $rules, $messages);

        DB::beginTransaction();
        try {
            $user = User::query()->where('id', '=', $id)->where('username', '<>', 'SysAdminSipongi')->firstOrFail();
            $user->email = $request->get('email');
            $user->name = $request->get('name');
            $user->regional_id = $request->get('regional_id');
            $user->provinsi_id = $request->get('provinsi_id');
            $user->unit_kerja = $request->get('unit_kerja');
            $user->keterangan = $request->get('keterangan');
            $user->username = $request->get('username');
            $user->is_super_admin = $request->get('is_super_admin');
            $user->status = $request->get('status');
            if ($request->filled('password')) {
                $user->password = Hash::make($request->get('password'));
            }
            $user->updated_at = Carbon::now();
            $user->old_tmp_role = $user->roles->first();
            $user->tmp_role = $request->get('role');

            $user->save();

            $user->syncRoles([$request->get('role')]);
        } catch (Exception $e) {
            DB::rollback();
            return response()->json(array(
                'status' => false,
                'message' => __('exception.handler.500'),
                'errors' => $e
            ), 500);
        }

        DB::commit();

        return response()->json(array('status' => true), 200);
    }

    public function destroy(Request $request)
    {
        DB::beginTransaction();
        try {
            $ids_to_delete = array_map(function ($item) {
                return $item['id'];
            }, $request->get('deleteSelected'));
            $deleted = User::query()->whereIn('id', $ids_to_delete)->where('username', '<>', 'SysAdminSipongi')->get();
            foreach ($deleted as $item) {
                $item->delete();
            }
        } catch (Exception $e) {
            DB::rollback();
            return response()->json(array(
                'status' => false,
                'message' => __('exception.handler.500'),
                'errors' => $e
            ), 500);
        }

        DB::commit();

        return response()->json(array('status' => true), 200);
    }

    public function restore(Request $request)
    {
        DB::beginTransaction();
        try {
            User::query()->onlyTrashed()->where('id', $request->get('id'))->where('username', '<>', 'SysAdminSipongi')->restore();
        } catch (Exception $e) {
            DB::rollback();
            return response()->json(array(
                'status' => false,
                'message' => __('exception.handler.500'),
                'errors' => $e
            ), 500);
        }

        DB::commit();

        return response()->json(array('status' => true), 200);
    }

    public function count()
    {
        $user = User::query()->where('username', '<>', 'SysAdminSipongi')->count();
        return response()->json($user);
    }
}
