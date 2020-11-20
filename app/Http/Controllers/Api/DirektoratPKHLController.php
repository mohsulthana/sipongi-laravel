<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Exceptions\Handler as Exception;
use App\Http\Resources\DirektoratPKHL\ListResource;
use App\Http\Resources\DirektoratPKHL\ListResources;
use App\Models\DirektoratPKHL;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DirektoratPKHLController extends Controller
{
    public function getDataApi()
    {
        $data = DirektoratPKHL::query()
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

        $query = DirektoratPKHL::query();

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
            'text'   => 'required',
            'logo'   => 'required|max:2100|mimes:jpeg,jpg,png',
            'active' => 'required|boolean',
        ];
        $this->validate($request, $rules);

        //Store data
        DB::beginTransaction();
        try {
            $store = DirektoratPKHL::create([
                'date'   => $request->date,
                'text'   => $request->text,
                'logo'   => 'logo',
                'active' => $request->active,
            ]);

            if ($request->hasFile('logo') && $store) {
                $file = $request->file('logo');
                $filename =  Str::orderedUuid() . '.' . $file->getClientOriginalExtension();
                $file->storeAs("nas/public/direktorat-pkhl", $filename);

                $store->logo = $filename;
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
        $data = DirektoratPKHL::query()
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
            'text'   => 'required',
            'logo'   => 'nullable|max:2100|mimes:jpeg,jpg,png',
            'active' => 'required|boolean',
        ];
        $this->validate($request, $rules);

        //Update data
        DB::beginTransaction();
        try {
            $update = DirektoratPKHL::query()->where('id', '=', $id)->firstOrFail();
            $update->date = $request->date;
            $update->text = $request->text;
            $update->active = $request->active;

            if ($request->hasFile('logo')) {
                $oldfileexists = Storage::disk('publicNas')->exists("direktorat-pkhl/$update->image");
                if ($oldfileexists) {
                    Storage::disk('publicNas')->delete("direktorat-pkhl/$update->image");
                }
                $file = $request->file('logo');
                $filename =  Str::orderedUuid() . '.' . $file->getClientOriginalExtension();
                $file->storeAs("nas/public/direktorat-pkhl", $filename);

                $update->logo = $filename;
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

            $data = DirektoratPKHL::query()->whereIn('id', $ids_to_delete)->get();
            foreach ($data as $key => $value) {
                $oldfileexists = Storage::disk('publicNas')->exists("direktorat-pkhl/$value->logo");
                if ($oldfileexists) {
                    Storage::disk('publicNas')->delete("direktorat-pkhl/$value->logo");
                }
            }

            if($data){
                DirektoratPKHL::query()->whereIn('id', $ids_to_delete)->delete();
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
