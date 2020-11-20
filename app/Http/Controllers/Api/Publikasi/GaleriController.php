<?php

namespace App\Http\Controllers\Api\Publikasi;

use App\Exceptions\Handler as Exception;
use App\Http\Controllers\Controller;
use App\Http\Resources\Publikasi\Galeri\ApiDetailResource;
use App\Http\Resources\Publikasi\Galeri\ApiListResources;
use App\Http\Resources\Publikasi\GaleriDetail\ApiListResources as DetailApiListResources;
use App\Http\Resources\Publikasi\GaleriDetail\ListResources;
use App\Models\Publikasi\Galeri;
use App\Models\Publikasi\GaleriDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class GaleriController extends Controller
{
    public function getAll($tipe)
    {
        $lists = Galeri::query()->where('tipe', $tipe)->orderBy('title')->get();
        return response()->json($lists);
    }

    public function store(Request $request)
    {
        $rules = [
            'title' => 'required|string',
            'tipe' => 'required|string',
        ];

        $messages = [
            'title.required' => __('validation.required', ['attribute' => 'Title']),
            'tipe.required' => __('validation.required', ['attribute' => 'Kategori']),
        ];

        $this->validate($request, $rules, $messages);

        DB::beginTransaction();

        try {
            $store = Galeri::create([
                'title' => $request->get('title'),
                'tipe' => $request->get('tipe')
            ]);
        } catch (Exception $e) {
            DB::rollback();
            return response()->json(array(
                'status' => false,
                'message' => __('exception.handler.500'),
                'errors' => $e
            ), 500);
        }

        DB::commit();
        return response()->json(array('status' => true, 'data' => $store), 200);
    }

    public function update(Request $request, $id)
    {
        $rules = [
            'title' => 'required|string',
            'tipe' => 'required|string',
        ];

        $messages = [
            'title.required' => __('validation.required', ['attribute' => 'Title']),
            'tipe.required' => __('validation.required', ['attribute' => 'Kategori']),
        ];

        $this->validate($request, $rules, $messages);

        DB::beginTransaction();
        try {
            $update = Galeri::query()->where('id', '=', $id)->firstOrFail();
            $update->title = $request->get('title');
            $update->tipe = $request->get('tipe');

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
        return response()->json(array('status' => true, 'data' => $update), 200);
    }

    public function destroy(Request $request)
    {
        DB::beginTransaction();
        try {
            $delete = Galeri::query()->where('id', $request->get('id'))->first();
            $delete->delete();
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

    public function detailGaleri()
    {
        $per_page = !request()->filled('per_page') ? 10 : (int) request('per_page');
        $direction = request()->query('direction');
        $sortBy = request()->query('sortBy');

        $query = GaleriDetail::query();
        $query->where('galeri_id', request('galeri_id'));

        $lists = $query->orderBy($sortBy, $direction)->paginate($per_page);

        return new ListResources($lists);
    }

    public function storeDetail(Request $request)
    {
        $rules = [
            'galeri_id' => 'required',
            'keterangan' => 'required',
            'image' => 'required',
            'image.*' => 'max:2100|mimes:jpeg,jpg,png',
        ];

        $messages = [
            'keterangan.required' => __('validation.required', ['attribute' => 'Keterangan']),
            'image.required' => __('validation.required', ['attribute' => 'Gambar']),
        ];

        $this->validate($request, $rules, $messages);

        DB::beginTransaction();
        try {
            if ($request->hasFile('image')) {
                foreach ($request->file('image') as $image) {
                    $store = GaleriDetail::create([
                        'galeri_id' => $request->get('galeri_id'),
                        'keterangan' => $request->get('keterangan'),
                    ]);
                    $filename =  Str::orderedUuid() . '.' . $image->getClientOriginalExtension();
                    $image->storeAs("nas/public/galeri/$store->galeri_id/images", $filename);
                    $store->image = $filename;
                    $store->save();
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

    public function updateDetail(Request $request, $id)
    {
        $rules = [
            'id' => 'required',
            'keterangan' => 'required',
        ];

        $messages = [
            'keterangan.required' => __('validation.required', ['attribute' => 'Keterangan']),
        ];

        $this->validate($request, $rules, $messages);

        DB::beginTransaction();
        try {
            $update = GaleriDetail::query()->where('id', '=', $id)->firstOrFail();
            $update->keterangan = $request->get('keterangan');

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

    public function destroyDetail(Request $request)
    {
        DB::beginTransaction();
        try {
            $delete = GaleriDetail::query()->where('id', $request->get('id'))->first();
            $delete->delete();
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

    public function publishDetail(Request $request)
    {
        DB::beginTransaction();
        try {
            $update = GaleriDetail::query()->where('id', $request->get('id'))->first();
            $update->publish = $request->get('publish');
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

    public function listGaleri()
    {
        $per_page = !request()->filled('per_page') ? 10 : (int) request('per_page');

        $query1 = GaleriDetail::query();
        $query1->with(['galeri']);
        $query1->select([
            'pub_galeri_detail.galeri_id',
            DB::raw("count(pub_galeri_detail.id) as count"),
        ]);
        $query1->join('pub_galeri as a', 'a.id', '=', 'pub_galeri_detail.galeri_id');
        $query1->where('a.tipe', request('tipe'));
        $query1->where('pub_galeri_detail.publish', true);
        $query1->whereNull('pub_galeri_detail.deleted_at');
        $query1->groupBy('pub_galeri_detail.galeri_id');

        $query = Galeri::query();
        $query->select([
            'pub_galeri.*'
        ]);
        $query->joinSub($query1, 'a', function ($join) {
            $join->on('pub_galeri.id', '=', 'a.galeri_id')
                ->where('a.count', '>', 0);
        });
        $query->where('pub_galeri.tipe', request('tipe'));

        $lists = $query->orderBy('pub_galeri.created_at', 'desc')->paginate($per_page);

        return new ApiListResources($lists);
    }

    public function detailGaleries($slug)
    {
        $query = Galeri::query();
        $query->where('slug', $slug);
        $lists = $query->firstOrFail();
        ApiDetailResource::withoutWrapping();

        return new ApiDetailResource($lists);
    }
}
