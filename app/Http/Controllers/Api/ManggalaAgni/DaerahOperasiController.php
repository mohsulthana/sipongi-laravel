<?php

namespace App\Http\Controllers\Api\ManggalaAgni;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Exceptions\Handler as Exception;
use App\Http\Resources\ManggalaAgni\DaerahOperasi\ListResource;
use App\Http\Resources\ManggalaAgni\DaerahOperasi\ListResources;
use Illuminate\Support\Facades\DB;
use App\Models\ManggalaAgni\DaerahOperasi;

class DaerahOperasiController extends Controller
{
    public function getApiData()
    {
        $daerah = DaerahOperasi::where('status',true)->OrderBy('created_at','ASC')->get();

        $daerah->transform(function($item){
            $d['daerah'] = $item->daerah;
            $d['kota'] = DaerahOperasi::where('parent_id',$item->id)->get();

            return $d;
        });

        return $daerah;
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

        $query = DaerahOperasi::query()->where('status',true);

        $lists = $query->orderBy($sortBy, $direction)->paginate($per_page);

        return new ListResources($lists);
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function daerah()
    {
        $per_page = !request()->filled('per_page') ? 10 : (int) request('per_page');
        $direction = request()->query('direction');
        $sortBy = request()->query('sortBy');

        $query = DaerahOperasi::query()
            ->where('parent_id',request()->parent_id)
            ->where('status',false);

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
        if($request->has('parent_id')){
            //Validation
            $rules = [
                'daerah'         => 'required|string',
                // 'telepon'        => 'required|string',
                // 'alamat'         => 'required|string',
                'jumlah_regu'    => 'required|integer',
                'jumlah_anggota' => 'required|integer',
            ];
        }else {
            //Validation
            $rules = [
                'daerah'         => 'required|string',
            ];
        }
        $this->validate($request, $rules);

        //Store data
        DB::beginTransaction();
        if($request->has('parent_id')){
            try {
                $store = DaerahOperasi::create([
                    'parent_id'     => $request->parent_id,
                    'status'        => false,
                    'daerah'        => $request->daerah,
                    'telepon'       => $request->telepon,
                    'alamat'        => $request->alamat,
                    'jumlah_regu'   => $request->jumlah_regu,
                    'jumlah_anggota' => $request->jumlah_anggota,
                ]);
    
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
            return response()->json(array('status' => true, 'parent_id' => $request->parent_id), 200);
        }else {
            try {
                $store = DaerahOperasi::create([
                    'daerah'   => $request->daerah,
                ]);
    
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
        

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data = DaerahOperasi::query()
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
        if($request->status){
            //Validation
            $rules = [
                'daerah'   => 'required|string',
            ];
        }else{
            //Validation
            $rules = [
                'daerah'         => 'required|string',
                // 'telepon'        => 'required|string',
                // 'alamat'         => 'required|string',
                'jumlah_regu'    => 'required|integer',
                'jumlah_anggota' => 'required|integer',
            ];
        }
        $this->validate($request, $rules);

        //Update data
        DB::beginTransaction();
        if($request->status){
            try {
                $update = DaerahOperasi::query()->where('id', '=', $id)->firstOrFail();
                $update->daerah    = $request->daerah;
    
                $update->save();
                DB::commit();
                return response()->json(array('status' => true), 200);
            } catch (Exception $e) {
                DB::rollback();
                return response()->json(array(
                    'status' => false,
                    'message' => __('exception.handler.500'),
                    'errors' => $e
                ), 500);
            }
        }else {
            try {
                $update = DaerahOperasi::query()->where('id', '=', $id)->firstOrFail();
                $update->daerah     = $request->daerah;
                $update->telepon    = $request->telepon;
                $update->alamat     = $request->alamat;
                $update->jumlah_regu     = $request->jumlah_regu;
                $update->jumlah_anggota  = $request->jumlah_anggota;
    
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
            return response()->json(array('status' => true, 'parent_id' => $update->parent_id ), 200);
        }

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

            foreach ($ids_to_delete as $key => $value) {
                $data = DaerahOperasi::find($value);
                if($data->status){
                    $_data = DaerahOperasi::query()->where('parent_id', $value)->delete();
                    $data->delete();
                }else{
                    DaerahOperasi::query()->where('id', $value)->delete();
                }
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
