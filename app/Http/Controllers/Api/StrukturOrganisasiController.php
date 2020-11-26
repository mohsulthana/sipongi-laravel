<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Exceptions\Handler as Exception;
use App\Http\Resources\StrukturOrganisasi\ListResource;
use App\Http\Resources\StrukturOrganisasi\ListResources;
use App\Models\StrukturOrganisasi;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class StrukturOrganisasiController extends Controller
{
    public function getDataApi()
    {
        $data = StrukturOrganisasi::query()
        ->where('active', '=', true)
        ->firstOrFail();
        ListResource::withoutWrapping();

        return new ListResource($data);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $per_page = !request()->filled('per_page') ? 10 : (int) request('per_page');
        $direction = request()->query('direction');
        $sortBy = request()->query('sortBy');

        $query = StrukturOrganisasi::query();

        $lists = $query->orderBy($sortBy, $direction)->paginate($per_page);

        return new ListResources($lists);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {   
        //Validation
        $rules = [
            'date'   => 'required|date',
            'image'  => 'required|max:2100|mimes:jpeg,jpg,png',
            'active' => 'required|boolean',
        ];
        $this->validate($request, $rules);

        //Store data
        DB::beginTransaction();
        try {
            $store = StrukturOrganisasi::create([
                'date'   => $request->date,
                'image'  => 'image',
                'active' => $request->active,
            ]);

            if ($request->hasFile('image') && $store) {
                $file = $request->file('image');
                $filename =  Str::orderedUuid() . '.' . $file->getClientOriginalExtension();
                $file->storeAs("nas/public/struktur-organisasi", $filename);

                $store->image = $filename;
                $store->save();
            }

            $store->save();
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

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data = StrukturOrganisasi::query()
            ->where('id', '=', $id)
            ->firstOrFail();
        ListResource::withoutWrapping();

        return new ListResource($data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //Validation
        $rules = [
            'date'   => 'required|date',
            'image'  => 'nullable|max:2100|mimes:jpeg,jpg,png',
            'active' => 'required|boolean',
        ];
        $this->validate($request, $rules);

        //Update data
        DB::beginTransaction();
        try {
            $update = StrukturOrganisasi::query()->where('id', '=', $id)->firstOrFail();
            $update->date = $request->date;
            $update->active = $request->active;

            if ($request->hasFile('image')) {
                $oldfileexists = Storage::disk('publicNas')->exists("struktur-organisasi/$update->image");
                if ($oldfileexists) {
                    Storage::disk('publicNas')->delete("struktur-organisasi/$update->image");
                }
                $file = $request->file('image');
                $filename =  Str::orderedUuid() . '.' . $file->getClientOriginalExtension();
                $file->storeAs("nas/public/struktur-organisasi", $filename);

                $update->image = $filename;
            }

            $update->save();

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

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        DB::beginTransaction();
        try {
            $ids_to_delete = array_map(function ($item) {
                return $item['id'];
            }, $request->get('deleteSelected'));

            $data = StrukturOrganisasi::query()->whereIn('id', $ids_to_delete)->get();
            foreach ($data as $key => $value) {
                $oldfileexists = Storage::disk('publicNas')->exists("struktur-organisasi/$value->image");
                if ($oldfileexists) {
                    Storage::disk('publicNas')->delete("struktur-organisasi/$value->image");
                }
            }

            if($data){
                StrukturOrganisasi::query()->whereIn('id', $ids_to_delete)->delete();
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
}
