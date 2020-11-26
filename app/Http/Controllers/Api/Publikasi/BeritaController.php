<?php

namespace App\Http\Controllers\Api\Publikasi;

use App\Exceptions\Handler as Exception;
use App\Http\Controllers\Controller;
use App\Http\Resources\Publikasi\Berita\ListResources;
use App\Http\Resources\Publikasi\Berita\ListResource;
use App\Http\Resources\Publikasi\Berita\ApiListResources;
use App\Http\Resources\Publikasi\Berita\ApiDetailResource;
use App\Models\Publikasi\Berita;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BeritaController extends Controller
{
    public function getDt()
    {
        $per_page = !request()->filled('per_page') ? 10 : (int) request('per_page');
        $direction = request()->query('direction');
        $sortBy = request()->query('sortBy');

        $query = Berita::query();
        $query->when(request()->filled('title'), function ($query) {
            return $query->where('title', 'ILIKE', "%" . request()->query('title') . "%");
        });
        $query->when(request()->filled('desc'), function ($query) {
            return $query->where('desc', 'ILIKE', "%" . request()->query('desc') . "%");
        });
        $query->when(request()->filled('publish'), function ($query) {
            return $query->where('publish', request()->query('publish'));
        });

        $lists = $query->orderBy($sortBy, $direction)->paginate($per_page);

        return new ListResources($lists);
    }


    public function store(Request $request)
    {
        $rules = [
            'title' => 'required|string',
            'desc' => 'required|string',
            'publish' => 'required|boolean',
            'image' => 'required|max:2100|mimes:jpeg,jpg,png',
        ];

        $messages = [
            'title.required' => __('validation.required', ['attribute' => 'Title']),
            'desc.required' => __('validation.required', ['attribute' => 'Content']),
            'publish.required' => __('validation.required', ['attribute' => 'publish']),
            'image.required' => __('validation.required', ['attribute' => 'Gambar']),
            'image.max' => __('validation.max.file', ['attribute' => 'Gambar', 'max' => '2', 'type' => 'MB']),
            'image.mimes' => __('validation.mimes', ['attribute' => 'Gambar', 'values' => 'JPG, JPEG, PNG']),
        ];

        $this->validate($request, $rules, $messages);

        DB::beginTransaction();

        try {
            $store = Berita::create([
                'title' => $request->get('title'),
                'desc' => $request->get('desc'),
                'publish' => (bool) $request->get('publish')
            ]);

            if ($request->hasFile('image') && $store) {
                $file = $request->file('image');
                $filename =  Str::orderedUuid() . '.' . $file->getClientOriginalExtension();
                $file->storeAs("nas/public/berita/$store->id/images", $filename);

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

    public function edit($id)
    {
        $data = Berita::query()
            ->where('id', '=', $id)
            ->firstOrFail();
        ListResource::withoutWrapping();

        return new ListResource($data);
    }

    public function update(Request $request, $id)
    {
        $rules = [
            'title' => 'required|string',
            'desc' => 'required|string',
            'publish' => 'required|boolean',
            'image' => 'nullable|max:2100|mimes:jpeg,jpg,png',
        ];

        $messages = [
            'title.required' => __('validation.required', ['attribute' => 'Title']),
            'desc.required' => __('validation.required', ['attribute' => 'Content']),
            'publish.required' => __('validation.required', ['attribute' => 'publish']),
            'image.max' => __('validation.max.file', ['attribute' => 'Gambar', 'max' => '2', 'type' => 'MB']),
            'image.mimes' => __('validation.mimes', ['attribute' => 'Gambar', 'values' => 'JPG, JPEG, PNG']),
        ];

        $this->validate($request, $rules, $messages);

        DB::beginTransaction();
        try {
            $update = Berita::query()->where('id', '=', $id)->firstOrFail();
            $update->title = $request->get('title');
            $update->desc = $request->get('desc');
            $update->publish = (bool) $request->get('publish');


            if ($request->hasFile('image')) {
                $oldfileexists = Storage::disk('publicNas')->exists("berita/$update->id/images/$update->image");
                if ($oldfileexists) {
                    Storage::disk('publicNas')->delete("berita/$update->id/images/$update->image");
                }
                $file = $request->file('image');
                $filename =  Str::orderedUuid() . '.' . $file->getClientOriginalExtension();
                $file->storeAs("nas/public/berita/$update->id/images", $filename);

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

    public function destroy(Request $request)
    {
        DB::beginTransaction();
        try {
            $ids_to_delete = array_map(function ($item) {
                return $item['id'];
            }, $request->get('deleteSelected'));

            Berita::query()->whereIn('id', $ids_to_delete)->delete();
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

    public function listBerita()
    {
        $fieldName = array(
            'created_at' => 'created_at'
        );
        $per_page = !request()->filled('per_page') ? 10 : (int) request('per_page');
        $direction = request()->query('direction');
        $sortBy = request()->query('sortBy');

        $query = Berita::query();

        $lists = $query->orderBy($fieldName[$sortBy], $direction)->paginate($per_page);

        return new ApiListResources($lists);
    }

    public function detailBerita($slug)
    {
        $query = Berita::query();
        $query->where('slug', $slug);
        $data = $query->firstOrFail();
        ApiDetailResource::withoutWrapping();

        return new ApiDetailResource($data);
    }
}
