<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Exceptions\Handler as Exception;
use App\Http\Resources\LaporanHarian\ListResource;
use App\Http\Resources\LaporanHarian\ListResources;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\LaporanHarian;

class LaporanHarianController extends Controller
{

    public function getDataApi()
    {
        $per_page = !request()->filled('per_page') ? 10 : (int) request('per_page');
        $direction = !request()->filled('direction') ? 'desc' : request()->query('direction');

        $query = LaporanHarian::query();

        $lists = $query->orderBy('bulan', $direction)->paginate($per_page);

        return new ListResources($lists);
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

        $query = LaporanHarian::query();

        $lists = $query->orderBy($sortBy, $direction)->paginate($per_page);

        return new ListResources($lists);
    }

    public function store(Request $request)
    {
        //Validation
        $rules = [
            'bulan'   => 'required|integer',
            'tahun'   => 'required|integer',
            'link'    => 'required|string',
        ];
        $this->validate($request, $rules);

        //Store data
        DB::beginTransaction();
        try {
            $store = LaporanHarian::create([
                'bulan' => $request->bulan,
                'bulan_nama' => $this->getNamaBulan($request->bulan),
                'tahun' => $request->tahun,
                'link'  => $request->link,
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

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data = LaporanHarian::query()
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
            'bulan'   => 'required|integer',
            'tahun'   => 'required|integer',
            'link'    => 'required|string',
        ];
        $this->validate($request, $rules);

        //Update data
        DB::beginTransaction();
        try {
            $update = LaporanHarian::query()->where('id', '=', $id)->firstOrFail();
            $update->bulan = $request->bulan;
            $update->bulan_nama = $this->getNamaBulan($request->bulan);
            $update->tahun = $request->tahun;
            $update->link  = $request->link;
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

    public function getNamaBulan($id)
    {
        $data = [
            '1'=>'Januari',
            '2'=>'Februari',
            '3'=>'Maret',
            '4'=>'April',
            '5'=>'Mei',
            '6'=>'Juni',
            '7'=>'Juli',
            '8'=>'Agustus',
            '9'=>'September',
            '10'=>'Oktober',
            '11'=>'November',
            '12'=>'Desember',
        ];

        return $data[$id]; 
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

            LaporanHarian::query()->whereIn('id', $ids_to_delete)->delete();
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
