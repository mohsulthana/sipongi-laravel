<?php

namespace App\Http\Controllers\Api\Publikasi;

use App\Exceptions\Handler as Exception;
use App\Http\Controllers\Controller;
use App\Http\Resources\Publikasi\PerpuKat\ApiListResources;
use App\Http\Resources\Publikasi\Perpu\ListResource;
use App\Http\Resources\Publikasi\Perpu\ListResources;
use App\Models\Publikasi\Perpu;
use App\Models\Publikasi\PerpuKategori;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class PerpuController extends Controller
{
    public function getDt()
    {
        $per_page = !request()->filled('per_page') ? 10 : (int) request('per_page');
        $direction = request()->query('direction');
        $sortBy = request()->query('sortBy');

        $query = Perpu::query();
        $query->select(['pub_perpu.*']);
        $query->with(['kategori']);
        $query->leftJoin('pub_perpu_kategori as pk', function ($join) {
            $join->on('pk.id', '=', 'pub_perpu.kategori_id');
        });
        $query->when(request()->filled('title'), function ($query) {
            return $query->where('pub_perpu.title', 'ILIKE', "%" . request()->query('title') . "%");
        });
        $query->when(request()->filled('nomor'), function ($query) {
            return $query->where('pub_perpu.nomor', 'ILIKE', "%" . request()->query('nomor') . "%");
        });
        $query->when(request()->filled('kategori_id'), function ($query) {
            return $query->where('pub_perpu.kategori_id', request()->query('kategori_id'));
        });

        $lists = $query->orderBy($sortBy, $direction)->paginate($per_page);

        return new ListResources($lists);
    }

    public function store(Request $request)
    {
        $rules = [
            'kategori_id' => 'required|string',
            'nomor' => 'required|string',
            'title' => 'required|string',
            'tipe' => 'required|string',
            'file' => "required_if:tipe,file|nullable|max:21000|mimes:pdf",
            'url' => 'required_if:tipe,url|nullable|string|active_url',
        ];

        $messages = [
            'kategori_id.required' => __('validation.required', ['attribute' => 'Kategori']),
            'nomor.required' => __('validation.required', ['attribute' => 'Nomor']),
            'title.required' => __('validation.required', ['attribute' => 'Title']),
            'tipe.required' => __('validation.required', ['attribute' => 'Tipe']),
            'file.required_if' => __('validation.required', ['attribute' => 'File']),
            'file.max' => __('validation.max.file', ['attribute' => 'File', 'max' => '20', 'type' => 'MB']),
            'file.mimes' => __('validation.mimes', ['attribute' => 'File', 'values' => 'PDF']),
            'url.required_if' => __('validation.required', ['attribute' => 'URL']),
            'url.active_url' => __('validation.active_url', ['attribute' => 'URL']),
        ];

        $this->validate($request, $rules, $messages);

        DB::beginTransaction();

        try {
            $store = Perpu::create([
                'kategori_id' => $request->get('kategori_id'),
                'nomor' => $request->get('nomor'),
                'title' => $request->get('title'),
                'tipe' => $request->get('tipe'),
            ]);

            if ($request->hasFile('file') && $store && $request->get('tipe') === 'file') {
                $file = $request->file('file');
                $filename =  Str::slug(Str::lower($store->title)) . '.' . $file->getClientOriginalExtension();
                $file->storeAs("nas/public/perpu/$store->id/file", $filename);

                $store->file = $filename;
            }

            if ($store && $request->get('tipe') === 'url') {
                $store->file = $request->get('url');
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

    public function edit($id)
    {
        $data = Perpu::query()
            ->where('id', '=', $id)
            ->firstOrFail();
        ListResource::withoutWrapping();

        return new ListResource($data);
    }

    public function update(Request $request, $id)
    {
        $rules = [
            'kategori_id' => 'required|string',
            'nomor' => 'required|string',
            'title' => 'required|string',
            'tipe' => 'required|string',
            'file' => "sometimes|nullable|max:21000|mimes:pdf",
            'url' => 'required_if:tipe,url|nullable|string|active_url',
        ];

        $messages = [
            'kategori_id.required' => __('validation.required', ['attribute' => 'Kategori']),
            'nomor.required' => __('validation.required', ['attribute' => 'Nomor']),
            'title.required' => __('validation.required', ['attribute' => 'Title']),
            'tipe.required' => __('validation.required', ['attribute' => 'Tipe']),
            'file.required' => __('validation.required', ['attribute' => 'File']),
            'file.max' => __('validation.max.file', ['attribute' => 'File', 'max' => '20', 'type' => 'MB']),
            'file.mimes' => __('validation.mimes', ['attribute' => 'File', 'values' => 'PDF']),
            'url.required_if' => __('validation.required', ['attribute' => 'URL']),
            'url.active_url' => __('validation.active_url', ['attribute' => 'URL']),
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        $validator->sometimes('file', 'required', function ($request) {
            return ($request->tipe === 'file' && $request->tipe_old !== 'file');
        });

        $validator->validate();

        DB::beginTransaction();
        try {
            $update = Perpu::query()->where('id', '=', $id)->firstOrFail();
            $update->kategori_id = $request->get('kategori_id');
            $update->nomor = $request->get('nomor');
            $update->title = $request->get('title');
            $update->tipe = $request->get('tipe');


            if ($request->hasFile('file') && $request->get('tipe') === 'file') {
                $oldfileexists = Storage::disk('publicNas')->exists("perpu/$update->id/file/$update->file");
                if ($oldfileexists) {
                    Storage::disk('publicNas')->delete("perpu/$update->id/file/$update->file");
                }
                $file = $request->file('file');
                $filename =  Str::slug(Str::lower($update->title)) . '.' . $file->getClientOriginalExtension();
                $file->storeAs("nas/public/perpu/$update->id/file", $filename);

                $update->file = $filename;
            }

            if ($request->get('tipe') === 'url') {
                $update->file = $request->get('url');
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

    public function destroy(Request $request)
    {
        DB::beginTransaction();
        try {
            $ids_to_delete = array_map(function ($item) {
                return $item['id'];
            }, $request->get('deleteSelected'));

            Perpu::query()->whereIn('id', $ids_to_delete)->delete();
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

    public function listPerpu()
    {
        $query = PerpuKategori::query();
        $query->with(['Perpu']);

        $lists = $query->get();

        return new ApiListResources($lists);
    }

    public function getFile($slug)
    {
        $query = Perpu::query();
        $query->where('slug', $slug);
        $data = $query->firstOrFail();

        $oldfileexists = Storage::disk('publicNas')->exists("perpu/$data->id/file/$data->file");
        if ($oldfileexists && $data->tipe === 'file') {
            return Storage::disk('publicNas')->response("perpu/$data->id/file/$data->file");
        } else {
            abort(404);
        }
    }
}
