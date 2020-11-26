<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Provinsi;
use App\Models\EmisiCo2Tahunan;
use App\Http\Resources\EmisiCo2Tahunan\ListResource;
use App\Http\Resources\EmisiCo2Tahunan\ListResources;
use App\Exceptions\Handler as Exception;
use Illuminate\Support\Facades\DB;

class EmisiCo2TahunanController extends Controller
{
    public function getDataApi()
    {
        // Get Data Lima Tahun Terakhir
        $tahunTinggi = EmisiCo2Tahunan::select('tahun')
            ->orderBy('tahun', 'desc')
            ->first();
        $dataTahun = [];
        $dataTotal = [];
        for ($i=0; $i < 6; $i++) { 
            $_tahun = $tahunTinggi->tahun - $i;
            $cek =EmisiCo2Tahunan::select('tahun')
            ->where('tahun', $_tahun)
            ->first();

            if($cek){
                $dataTahun[] = $_tahun;
            }
        }

        sort($dataTahun);
        // Get Dat Total Kebakaran
        foreach ($dataTahun as $tahun) {
            $dataTotal[] = $this->getDataTotalKebakaran($tahun);
        }

        // Get Data Luas Kabakaran
        $dataNomor = [];
        $dataKebakaran = [];
        $provinsi = Provinsi::select('id','nama_provinsi')->orderBy('nama_provinsi','ASC')->get();
        foreach ($provinsi as $key => $prov) {
            $_dataLuas = [];
            foreach ($dataTahun as $tahun) {
                $_dataLuas[] = $this->getDataPerProvinsi($prov->id,$tahun);
            }
            //$dataNomor[] = $key+1;
            $dataKebakaran[$prov->nama_provinsi]= $_dataLuas;
        }

        return [
            //'nomor' => $dataNomor,
            'tahun' => $dataTahun,
            'data'  => $dataKebakaran,
            'total' => $dataTotal
        ];
    }

    public function getDataTotalKebakaran($year)
    {
        $data = EmisiCo2Tahunan::where('tahun',$year)->sum('total');

        return [
            'tahun' => $year,
            'total' => $data
        ];
    }

    public function getDataPerProvinsi($provinsi_id,$year)
    {
        $luas = EmisiCo2Tahunan::where([
            'tahun'      => $year,
            'provinsi_id' => $provinsi_id,
        ])->first();

        if($luas){
            return [
                'tahun' => $year,
                'luas' => $luas->total
            ];
        }

        return [
            'tahun' => $year,
            'total' => 0
        ];
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

        $query = EmisiCo2Tahunan::query();

        $query->when(request()->filled('tahun'), function ($query) {
            return $query->where('tahun', 'ILIKE', "%" . request()->query('tahun') . "%");
        });

        if(request()->has('provinsi')){
            $query->when(request()->filled('provinsi'), function ($query) {
                return $query->whereHas('Provinsi',function($q){
                    return $q->where('nama_provinsi', 'ILIKE', "%" . request()->query('provinsi') . "%");
                });
            });
        }

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
            'provinsi_id' => 'required',
            'tahun'       => 'required|integer',
            'total'       => 'required',
        ];
        $this->validate($request, $rules);

        //Store data
        DB::beginTransaction();
        try {
            $store = EmisiCo2Tahunan::create([
                'provinsi_id' => $request->provinsi_id,
                'tahun'       => $request->tahun,
                'total'       => $request->total,
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
        $data = EmisiCo2Tahunan::query()
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
            'provinsi_id' => 'required',
            'tahun'       => 'required|integer',
            'total'       => 'required',
        ];
        $this->validate($request, $rules);

        //Update data
        DB::beginTransaction();
        try {
            $update = EmisiCo2Tahunan::query()->where('id', '=', $id)->firstOrFail();
            $update->provinsi_id = $request->provinsi_id;
            $update->tahun  = $request->tahun;
            $update->total  = $request->total;
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

            EmisiCo2Tahunan::query()->whereIn('id', $ids_to_delete)->delete();
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
