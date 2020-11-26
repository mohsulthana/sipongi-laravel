<?php

namespace App\Http\Controllers\Api\Hotspot;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Exceptions\Handler as Exception;
use App\Http\Resources\Hotspot\Satelit\ListResource;
use App\Http\Resources\Hotspot\Satelit\ListResources;
use Illuminate\Support\Facades\DB;
use App\Models\HotspotSatelit;
use App\Models\Provinsi;
use App\Models\KotaKab;
use App\Models\Kecamatan;
use App\Models\Kelurahan;
use App\Traits\ImportHotspot;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;

class SatelitController extends Controller
{
    use ImportHotspot;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $per_page = !request()->filled('per_page') ? 10 : (int) request('per_page');
        $direction = !request()->filled('direction')? 'DESC' :  request()->query('direction');
        $sortBy = !request()->filled('sortBy') ? 'created_at' : request()->query('sortBy');

        $query = HotspotSatelit::query();

        $query->when(request()->filled('sumber'), function ($query) {
            return $query->where('sumber', 'ILIKE', "%" . request()->query('sumber') . "%");
        });

        $query->when(request()->filled('source'), function ($query) {
            return $query->where('source', 'ILIKE', "%" . request()->query('source') . "%");
        });

        $query->when(request()->filled('date_hotspot'), function ($query) {
            return $query->where('date_hotspot', 'ILIKE', "%" . request()->query('date_hotspot') . "%");
        });

        $query->when(request()->filled('provinsi'), function ($query) {
            return $query->where('provinsi', 'ILIKE', "%" . request()->query('provinsi') . "%");
        });

        $query->when(request()->filled('kabkota'), function ($query) {
            return $query->where('kabkota', 'ILIKE', "%" . request()->query('kabkota') . "%");
        });

        $query->when(request()->filled('kecamatan'), function ($query) {
            return $query->where('kecamatan', 'ILIKE', "%" . request()->query('kecamatan') . "%");
        });

        $query->when(request()->filled('desa'), function ($query) {
            return $query->where('desa', 'ILIKE', "%" . request()->query('desa') . "%");
        });

        $lists = $query->orderBy($sortBy, $direction)->paginate($per_page);

        return new ListResources($lists);
    }

    public function store(Request $request)
    {
        //Validation
        $rules = [
            'provinsi_id'   => 'required',
            'kotakab_id'    => 'required',
            'kecamatan_id'  => 'required',
            'kelurahan_id'  => 'required',
            'x'             => 'required|numeric',
            'y'             => 'required|numeric',
            'sumber'        => 'required',
            'source'        => 'required',
            'confidence'    => 'required|integer',
            'brightness'        => 'required|numeric',
            'date_hotspot'      => 'required|date',
        ];
        $this->validate($request, $rules);
        
        //Set Data
        $data = $request->all();
        
        $x = (float) $request->x; //longitude
        $y = (float) $request->y; //latitude

        $data['provinsi']   = Provinsi::find($request->provinsi_id)->nama_provinsi;
        $data['kabkota']    = KotaKab::find($request->kotakab_id)->nama;
        $data['kecamatan']  = Kecamatan::find($request->kecamatan_id)->nama;
        $data['desa']       = Kelurahan::find($request->kelurahan_id)->nama;

        $fungsiKawasan = $this->fungsiKawasan($x, $y);
        $petaKawasan = $this->petaKawasan($x, $y);

        $data['kawasan']        = $fungsiKawasan ? $fungsiKawasan->kawasan : null;
        $data['nama_hti']       = $fungsiKawasan ? $fungsiKawasan->nama_hti : null;
        $data['nama_ha']        = $fungsiKawasan ? $fungsiKawasan->nama_ha : null;
        $data['nama_kebun']     = $fungsiKawasan ? $fungsiKawasan->nama_kebun : null;
        $data['fungsi_kawasan'] = $petaKawasan ? $petaKawasan->fungsi : null;
        $data['sk_kawasan']     = $petaKawasan ? $petaKawasan->sk_kawasan : null;

        if ((int)$request->confidence === 7) {
            $level = 'low';
        } else if ((int) $request->confidence === 8) {
            $level = 'medium';
        } else if ((int) $request->confidence === 9) {
            $level = 'high';
        }
        $data['confidence_level'] = $level;
        $data['geom'] = DB::raw("ST_GeomFromText('POINT(' || $x || ' ' || $y || ')',4326)");
        
        // Store data
        DB::beginTransaction();
        try {
            $store = HotspotSatelit::create($data);
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

    public function getDataProvinsi()
    {
        $data = Provinsi::select('id','nama_provinsi')->get();
        $data->transform(function($item){
            $d['value'] = $item->id;
            $d['text']  = $item->nama_provinsi;

            return $d;
        });

        return $data;
    }

    public function getDataKabupaten($provinsi_id)
    {
        $data = KotaKab::select('id','nama')
            ->where('provinsi_id',$provinsi_id)
            ->get();
        $data->transform(function($item){
            $d['value'] = $item->id;
            $d['text']  = $item->nama;

            return $d;
        });

        return $data;
    }

    public function getDataKecamatan($kotakab_id)
    {
        $data = Kecamatan::select('id','nama')
            ->where('kotakab_id',$kotakab_id)
            ->get();
        $data->transform(function($item){
            $d['value'] = $item->id;
            $d['text']  = $item->nama;

            return $d;
        });

        return $data;
    }

    public function getDataDesa($kecamatan_id)
    {
        $data = Kelurahan::select('id','nama')
            ->where('kecamatan_id',$kecamatan_id)
            ->get();
        $data->transform(function($item){
            $d['value'] = $item->id;
            $d['text']  = $item->nama;

            return $d;
        });

        return $data;
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

            HotspotSatelit::query()->whereIn('id', $ids_to_delete)->forceDelete();
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
