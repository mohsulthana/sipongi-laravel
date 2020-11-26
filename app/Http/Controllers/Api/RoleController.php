<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\Handler as Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\Role\DetailRoleResource;
use App\Http\Resources\Role\RoleResources;
use App\Models\Spatie\Role;
use Illuminate\Support\Facades\DB;

class RoleController extends Controller
{
    public function getDt()
    {
        $per_page = !request()->filled('per_page') ? 10 : (int) request('per_page');
        $direction = request()->query('direction');
        $sortBy = request()->query('sortBy');

        $query = Role::query();
        $query->with(['users', 'users.roles']);
        $query->withCount(['permissions', 'users']);

        $roles = $query->orderBy($sortBy, $direction)->paginate($per_page);

        return new RoleResources($roles);
    }

    public function store(Request $request)
    {
        $rules = [
            'name' => 'required|string|unique:roles',
            'display_name' => 'required|string|unique:roles',
            'selectedPermissions' => 'array'
        ];

        $messages = [
            'name.required' => __('validation.required', ['attribute' => __('fields.slug')]),
            'name.string' => __('validation.string', ['attribute' => __('fields.slug')]),
            'name.unique' => __('validation.unique', ['attribute' => __('fields.slug')]),
            'display_name.required' => __('validation.required', ['attribute' => __('fields.roles_name')]),
            'display_name.string' => __('validation.string', ['attribute' => __('fields.roles_name')]),
            'display_name.unique' => __('validation.unique', ['attribute' => __('fields.roles_name')]),
            'selectedPermissions.array' => __('validation.array', ['attribute' => __('fields.permission')]),
        ];

        $this->validate($request, $rules, $messages);


        DB::beginTransaction();
        try {
            $role = Role::create([
                'name' => $request->get('name'),
                'display_name' => $request->get('display_name'),
                'guard_name' => config('auth.defaults.guard')
            ]);

            if (count($request->get('selectedPermissions')) > 0) {
                $role->permissions()->sync($request->get('selectedPermissions'));
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
        app('cache')
            ->store(config('permission.cache.store') != 'default' ? config('permission.cache.store') : null)
            ->forget(config('permission.cache.key'));

        return response()->json(array('status' => true), 200);
    }

    public function edit($id)
    {
        $role = Role::query()->with(['permissions'])->where('name', '<>', 'member')->findOrFail($id);
        DetailRoleResource::withoutWrapping();

        return new DetailRoleResource($role);
    }

    public function update(Request $request, $id)
    {
        $rules = [
            'name' => 'required|string|unique:roles,name,' . $id,
            'display_name' => 'required|string|unique:roles,display_name,' . $id,
            'selectedPermissions' => 'array'
        ];

        $messages = [
            'name.required' => __('validation.required', ['attribute' => __('fields.slug')]),
            'name.string' => __('validation.string', ['attribute' => __('fields.slug')]),
            'name.unique' => __('validation.unique', ['attribute' => __('fields.slug')]),
            'display_name.required' => __('validation.required', ['attribute' => __('fields.roles_name')]),
            'display_name.string' => __('validation.string', ['attribute' => __('fields.roles_name')]),
            'display_name.unique' => __('validation.unique', ['attribute' => __('fields.roles_name')]),
            'selectedPermissions.array' => __('validation.array', ['attribute' => __('fields.permission')]),
        ];

        $this->validate($request, $rules, $messages);

        DB::beginTransaction();
        try {
            $role = Role::query()->findOrFail($id);

            if ($role->name != $request->get('name')) {
                $role->name = $request->get('name');
            }

            if ($role->display_name != $request->get('display_name')) {
                $role->display_name = $request->get('display_name');
            }

            // if (count($request->get('selectedPermissions')) > 0) {
            $role->permissions()->sync($request->get('selectedPermissions'));
            // }

            $role->save();
        } catch (Exception $e) {
            DB::rollback();
            return response()->json(array(
                'status' => false,
                'message' => __('exception.handler.500'),
                'errors' => $e
            ), 500);
        }

        DB::commit();
        app('cache')
            ->store(config('permission.cache.store') != 'default' ? config('permission.cache.store') : null)
            ->forget(config('permission.cache.key'));

        return response()->json(array('status' => true), 200);
    }

    public function destroy(Request $request)
    {
        DB::beginTransaction();
        try {
            $ids_to_delete = array_map(function ($item) {
                return $item['id'];
            }, $request->get('deleteSelected'));
            Role::query()->whereIn('id', $ids_to_delete)->delete();
        } catch (Exception $e) {
            DB::rollback();
            return response()->json(array(
                'status' => false,
                'message' => __('exception.handler.500'),
                'errors' => $e
            ), 500);
        }

        DB::commit();
        app('cache')
            ->store(config('permission.cache.store') != 'default' ? config('permission.cache.store') : null)
            ->forget(config('permission.cache.key'));

        return response()->json(array('status' => true), 200);
    }

    public function getAll()
    {
        $role = Role::query()->where('name', '<>', 'member')->orderBy('display_name')->get();
        return response()->json($role);
    }
}
