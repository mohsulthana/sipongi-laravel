<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\Handler as Exception;
use App\Http\Controllers\Controller;
use App\Http\Resources\LuasKebakaran\EntriLuasKebakaranReseources;
use App\Http\Resources\LuasKebakaran\EntriLuasKebakaranReseource;
use App\Http\Resources\LuasKebakaran\ListResource;
use App\Http\Resources\LuasKebakaran\ListResources;
use App\Models\LuasKebakaranTahunan;
use App\Models\Provinsi;
use App\Traits\HotspotSatelitTrait;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LuasKebakaranTahunanController extends Controller
{
    use HotspotSatelitTrait;

    public function getDataApi()
    {
        // Get Data Lima Tahun Terakhir
        $tahunTinggi = LuasKebakaranTahunan::select('tahun')
            ->orderBy('tahun', 'desc')
            ->first();
        $dataTahun = [];
        $dataTotal = [];
        for ($i=0; $i < 6; $i++) { 
            $_tahun = $tahunTinggi->tahun - $i;
            $cek =LuasKebakaranTahunan::select('tahun')
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
        $data = LuasKebakaranTahunan::where('tahun',$year)->sum('luas_kebakaran');

        return [
            'tahun' => $year,
            'total' => $data
        ];
    }

    public function getDataPerProvinsi($provinsi_id,$year)
    {
        $luas = LuasKebakaranTahunan::where([
            'tahun'      => $year,
            'provinsi_id' => $provinsi_id,
        ])->first();

        if($luas){
            return [
                'tahun' => $year,
                'luas' => $luas->luas_kebakaran
            ];
        }

        return [
            'tahun' => $year,
            'total' => 0
        ];
    }

    public function index(Request $request)
    {
        $per_page = !request()->filled('per_page') ? 10 : (int) request('per_page');
        $direction = 'desc';
        $sortBy = 'tahun';

        $query = LuasKebakaranTahunan::query();

        if (request()->has('tahun')) {
            $query->when(request()->filled('tahun'), function ($query) {
                return $query->where('tahun', 'ILIKE', "%" . request()->query('tahun') . "%");
            });
        }

        if (request()->has('provinsi')) {
            $query->when(request()->filled('provinsi'), function ($query) {
                return $query->whereHas('Provinsi', function ($q) {
                    return $q->where('nama_provinsi', 'ILIKE', "%" . request()->query('provinsi') . "%");
                });
            });
        }

        $lists = $query->orderBy($sortBy, $direction)->paginate($per_page);


        return new ListResources($lists);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data = LuasKebakaranTahunan::query()
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
    public function updateData(Request $request, $id)
    {
        //Validation
        $rules = [
            'provinsi_id'    => 'required',
            'tahun'          => 'required|integer',
            'luas_kebakaran' => 'required',
        ];
        $this->validate($request, $rules);

        //Update data
        DB::beginTransaction();
        try {
            $update = LuasKebakaranTahunan::find($id);
            $update->tahun          = $request->tahun;
            $update->luas_kebakaran = floatval($request->luas_kebakaran);
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
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //Validation
        $rules = [
            'provinsi_id'    => 'required',
            'tahun'          => 'required|integer',
            'luas_kebakaran' => 'required',
        ];
        $this->validate($request, $rules);

        //Store data
        DB::beginTransaction();
        try {
            $store = LuasKebakaranTahunan::create([
                'provinsi_id'    => $request->provinsi_id,
                'tahun'          => $request->tahun,
                'luas_kebakaran' => $request->luas_kebakaran,
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

            LuasKebakaranTahunan::query()->whereIn('id', $ids_to_delete)->delete();
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

    public function getTotalLuasKebakaran()
    {
        $year = !request()->filled('year') ? Carbon::now()->format('Y') : (int) request('year');
        $short = !request()->filled('short') ? true : (bool) request('short');

        $query = LuasKebakaranTahunan::query();
        $query->where('tahun', $year);

        $sum = $query->sum('luas_kebakaran');

        if ($short) {
            $sum = $this->number_format_short($sum);
        }

        return response()->json($sum);
    }

    public function getGrafikLuasKebakaran()
    {
        $now = Carbon::now();

        $query = LuasKebakaranTahunan::query();
        $query->select([
            DB::raw("sum(luas_kebakaran) as value"),
            DB::raw("TO_DATE(CAST(tahun AS text), 'YYYY') as date"),
            DB::raw("TO_CHAR(TO_DATE(CAST(tahun AS text), 'YYYY'), 'YYYY') as year"),
        ]);
        $query->where('tahun', '>=', $now->copy()->subYears(3)->format('Y'));
        $query->groupBy(['tahun']);

        $lists = $query->get();

        $res = collect(CarbonPeriod::create($now->copy()->subYears(3)->startOfYear()->format('Y-m-d'), "1 year", $now->copy()->startOfYear()->format('Y-m-d')))
            ->map(function ($date) {
                return [
                    'value' => 0,
                    'date' => $date->format('Y-m-d'),
                    'year' => $date->format('Y')
                ];
            })
            ->keyBy('date')
            ->merge(
                $lists->keyBy('date')
            )
            ->sortKeys();

        return response()->json($res->values());
    }

    public function getDt()
    {
        $year = !request()->filled('year') ? Carbon::now()->format('Y') : (int) request('year');

        $query = Provinsi::query();
        $query->selectRaw("
            provinsi.id as id,
            provinsi.nama_provinsi as nama,
            a.luas_kebakaran as luas
        ");
        $query->leftJoin('luas_kebakaran_tahunan as a', function ($join) use ($year) {
            $join->on('a.provinsi_id', '=', 'provinsi.id')
                ->where('a.tahun', $year);
        });
        $query->orderBy('provinsi.nama_provinsi');

        $lists = $query->get();

        return new EntriLuasKebakaranReseources($lists);
    }

    public function update(Request $request)
    {
        $rules = [
            'tahun' => 'required|integer',
            'provinsi.*.luas' => 'required|numeric',
        ];

        $messages = [
            'tahun.required' => __('validation.required', ['attribute' => "Tahun"]),
            'tahun.integer' => __('validation.integer', ['attribute' => "Tahun"]),
            'provinsi.*.luas.required' => __('validation.required', ['attribute' => 'Luas Kebakaran']),
            'provinsi.*.luas.numeric' => __('validation.numeric', ['attribute' => 'Luas Kebakaran']),
        ];

        $this->validate($request, $rules, $messages);

        DB::beginTransaction();

        try {
            $provinsi = $request->get('provinsi');

            foreach ($provinsi as $key => $val) {
                $update = LuasKebakaranTahunan::query()->where('provinsi_id', $val['id'])->where('tahun', $request->get('tahun'))->first();
                if (!$update) {
                    $update = new LuasKebakaranTahunan;
                    $update->provinsi_id = $val['id'];
                    $update->tahun = $request->get('tahun');
                }

                $update->luas_kebakaran = (float) $val['luas'];
                $update->save();
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
