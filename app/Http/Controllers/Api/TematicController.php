<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\Handler as Exception;
use App\Http\Controllers\Controller;
use App\Http\Resources\Tematic\ListResources;
use App\Http\Resources\Tematic\ListResource;
use App\Http\Resources\Tematic\ApiListResource;
use App\Http\Resources\Tematic\ApiListResources;
use App\Models\Tematic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class TematicController extends Controller
{
    public function getDt()
    {
        $per_page = !request()->filled('per_page') ? 10 : (int) request('per_page');
        $direction = request()->query('direction');
        $sortBy = request()->query('sortBy');

        $query = Tematic::query();
        $query->when(request()->filled('title'), function ($query) {
            return $query->where('title', 'ILIKE', "%" . request()->query('title') . "%");
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
            'image' => 'required|max:2100|mimes:jpeg,jpg,png',
            'publish' => 'required|boolean',
        ];

        $messages = [
            'title.required' => __('validation.required', ['attribute' => 'Title']),
            'image.required' => __('validation.required', ['attribute' => 'Gambar']),
            'publish.required' => __('validation.required', ['attribute' => 'publish']),
            'image.max' => __('validation.max.file', ['attribute' => 'Gambar', 'max' => '2', 'type' => 'MB']),
            'image.mimes' => __('validation.mimes', ['attribute' => 'Gambar', 'values' => 'JPG, JPEG, PNG']),
        ];

        $this->validate($request, $rules, $messages);

        DB::beginTransaction();

        try {
            $store = Tematic::create([
                'title' => $request->get('title'),
                'publish' => (bool) $request->get('publish')
            ]);

            if ($request->hasFile('image') && $store) {
                $file = $request->file('image');
                $filename =  Str::orderedUuid() . '.' . $file->getClientOriginalExtension();
                $file->storeAs("nas/public/tematic/$store->id/images", $filename);

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
        $data = Tematic::query()
            ->where('id', '=', $id)
            ->firstOrFail();
        ListResource::withoutWrapping();

        return new ListResource($data);
    }

    public function update(Request $request, $id)
    {
        $rules = [
            'title' => 'required|string',
            'image' => 'nullable|max:2100|mimes:jpeg,jpg,png',
            'publish' => 'required|boolean',
        ];

        $messages = [
            'title.required' => __('validation.required', ['attribute' => 'Title']),
            'publish.required' => __('validation.required', ['attribute' => 'publish']),
            'image.max' => __('validation.max.file', ['attribute' => 'Gambar', 'max' => '2', 'type' => 'MB']),
            'image.mimes' => __('validation.mimes', ['attribute' => 'Gambar', 'values' => 'JPG, JPEG, PNG']),  
        ];

        $this->validate($request, $rules, $messages);

        DB::beginTransaction();
        try {
            $update = Tematic::query()->where('id', '=', $id)->firstOrFail();
            $update->title = $request->get('title');
            $update->publish = (bool) $request->get('publish');

            if ($request->hasFile('image')) {
                $oldfileexists = Storage::disk('publicNas')->exists("tematic/$update->id/images/$update->image");
                if ($oldfileexists) {
                    Storage::disk('publicNas')->delete("tematic/$update->id/images/$update->image");
                }
                $file = $request->file('image');
                $filename =  Str::orderedUuid() . '.' . $file->getClientOriginalExtension();
                $file->storeAs("nas/public/tematic/$update->id/images", $filename);

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

            Tematic::query()->whereIn('id', $ids_to_delete)->delete();
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

    public function getDataApi()
    {
        $data = Tematic::query()->get();
         
        return new ApiListResources($data);
    }
}

