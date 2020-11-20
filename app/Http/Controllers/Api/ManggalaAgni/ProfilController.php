<?php

namespace App\Http\Controllers\Api\ManggalaAgni;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Exceptions\Handler as Exception;
use App\Http\Resources\ManggalaAgni\Profil\ListResource;
use App\Http\Resources\ManggalaAgni\Profil\ListResources;
use Illuminate\Support\Facades\DB;
use App\Models\ManggalaAgni\Profil;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProfilController extends Controller
{
    public function getDataApi()
    {
        $data = Profil::orderBy('urutan','ASC')->get();

        return new ListResources($data);
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

        $query = Profil::query()->orderBy('urutan','ASC');

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
            'title'  => 'required|string',
            'text'   => 'required|string',
            'urutan' => 'required|integer',
            'image'   => 'nullable|max:2100|mimes:jpeg,jpg,png',
        ];
        $this->validate($request, $rules);

        //Store data
        DB::beginTransaction();
        try {
            $store = Profil::create([
                'title'  => $request->title,
                'text'   => $request->text,
                'urutan' => $request->urutan,
            ]);

            if ($request->hasFile('image') && $store) {
                $file = $request->file('image');
                $filename =  Str::orderedUuid() . '.' . $file->getClientOriginalExtension();
                $file->storeAs("nas/public/manggala-agni", $filename);

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
        $data = Profil::query()
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
            'title'   => 'required|string',
            'text'   => 'required|string',
            'urutan' => 'required|integer',
            'image'   => 'nullable|max:2100|mimes:jpeg,jpg,png',
        ];
        $this->validate($request, $rules);

        //Update data
        DB::beginTransaction();
        try {
            $update = Profil::query()->where('id', '=', $id)->firstOrFail();
            $update->title  = $request->title;
            $update->text   = $request->text;
            $update->urutan = $request->urutan;

            if ($request->hasFile('image')) {
                $oldfileexists = Storage::disk('publicNas')->exists("manggala-agni/$update->image");
                if ($oldfileexists) {
                    Storage::disk('publicNas')->delete("manggala-agni/$update->image");
                }
                $file = $request->file('image');
                $filename =  Str::orderedUuid() . '.' . $file->getClientOriginalExtension();
                $file->storeAs("nas/public/manggala-agni", $filename);

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

            Profil::query()->whereIn('id', $ids_to_delete)->delete();
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
