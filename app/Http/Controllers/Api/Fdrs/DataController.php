<?php

namespace App\Http\Controllers\Api\Fdrs;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\Fdrs\ListResource;
use App\Http\Resources\Fdrs\ListResources;
use App\Models\Fdrs\DataFdrs;
use App\Exceptions\Handler as Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class DataController extends Controller
{
    public function index()
    {
        $per_page = !request()->filled('per_page') ? 10 : (int) request('per_page');
        // $direction = request()->query('direction');
        // $sortBy    = request()->query('sortBy');

        $query = DataFdrs::query();

        $query->when(request()->filled('date'), function ($query) {
            return $query->where('date', 'ILIKE', "%" . request()->query('date') . "%");
        });

        $query->when(request()->filled('fdrs_option_wilayah_key'), function ($query) {
            return $query->whereHas('Wilayah',function($q){
                return $q->where('nama', 'ILIKE', "%" . request()->query('fdrs_option_wilayah_key') . "%");
            });
        });

        $lists = $query->orderBy('fdrs_option_wilayah_key', 'ASC')
            ->orderBy('date', 'DESC')
            ->paginate($per_page);

        return new ListResources($lists);
    }

    public function detail($id)
    {
        $data = DataFdrs::query()
            ->where('id', '=', $id)
            ->firstOrFail();
        ListResource::withoutWrapping();

        return new ListResource($data);
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

            $data = DataFdrs::query()->whereIn('id', $ids_to_delete)->get();
            foreach ($data as $key => $value) {
                $oldfileexists = Storage::disk('publicNas')->exists("fdrs/$value->fdrs_option_wilayah_key/$value->image");
                if ($oldfileexists) {
                    Storage::disk('publicNas')->delete("fdrs/$value->fdrs_option_wilayah_key/$value->image");
                }
            }

            if($data){
                DataFdrs::query()->whereIn('id', $ids_to_delete)->delete();
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
