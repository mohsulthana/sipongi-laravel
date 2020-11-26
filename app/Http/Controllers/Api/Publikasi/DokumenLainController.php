<?php

namespace App\Http\Controllers\Api\Publikasi;

use App\Exceptions\Handler as Exception;
use App\Http\Controllers\Controller;
use App\Http\Resources\Publikasi\DokumenLain\ApiListResources;
use App\Http\Resources\Publikasi\DokumenLain\ListResource;
use App\Http\Resources\Publikasi\DokumenLain\ListResources;
use App\Models\Publikasi\DokumenLain;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class DokumenLainController extends Controller
{
    public function getDt()
    {
        $per_page = !request()->filled('per_page') ? 10 : (int) request('per_page');
        $direction = request()->query('direction');
        $sortBy = request()->query('sortBy');

        $querySub1 = DokumenLain::query();
        $querySub1->when(request()->filled('title'), function ($query) {
            return $query->where('title', 'ILIKE', "%" . request()->query('title') . "%");
        });
        $querySub1->where('private', true);
        $querySub1->where('user_id', auth()->user()->id);

        $querySub2 = DokumenLain::query();
        $querySub2->union($querySub1);
        $querySub2->where('private', false);

        $query = DokumenLain::query();
        $query->select([
            'pub_dokumen_lain.*'
        ]);
        $query->when(!auth()->user()->is_super_admin, function ($query) use ($querySub2) {
            return  $query->rightJoinSub($querySub2, 'b', function ($join) {
                $join->on('b.id', '=', 'pub_dokumen_lain.id');
            });
        });
        $query->when(request()->filled('title'), function ($query) {
            return $query->where('pub_dokumen_lain.title', 'ILIKE', "%" . request()->query('title') . "%");
        });
        $query->when(request()->filled('private'), function ($query) {
            return $query->where('pub_dokumen_lain.private', request()->query('private'));
        });

        $lists = $query->orderBy($sortBy, $direction)->paginate($per_page);

        return new ListResources($lists);
    }

    public function store(Request $request)
    {
        $rules = [
            'title' => 'required|string',
            'tipe' => 'required|string',
            'private' => 'required|boolean',
            'file' => "required_if:tipe,file|nullable|max:21000|mimes:doc,docx,ppt,pptx,xls,xlsx,pdf",
            'url' => 'required_if:tipe,url|nullable|string|active_url',
        ];

        $messages = [
            'title.required' => __('validation.required', ['attribute' => 'Title']),
            'tipe.required' => __('validation.required', ['attribute' => 'Tipe']),
            'private.required' => __('validation.required', ['attribute' => 'Private']),
            'file.required_if' => __('validation.required', ['attribute' => 'File']),
            'file.max' => __('validation.max.file', ['attribute' => 'File', 'max' => '20', 'type' => 'MB']),
            'file.mimes' => __('validation.mimes', ['attribute' => 'File', 'values' => 'DOCX, PPTX, XSLX, PDF']),
            'url.required_if' => __('validation.required', ['attribute' => 'URL']),
            'url.active_url' => __('validation.active_url', ['attribute' => 'URL']),
        ];

        $this->validate($request, $rules, $messages);

        DB::beginTransaction();

        try {
            $store = DokumenLain::create([
                'user_id' => $request->user()->id,
                'title' => $request->get('title'),
                'tipe' => $request->get('tipe'),
                'private' => (bool) $request->get('private')
            ]);

            if ($request->hasFile('file') && $store && $request->get('tipe') === 'file') {
                $file = $request->file('file');
                $filename =  Str::slug(Str::lower($store->title)) . '.' . $file->getClientOriginalExtension();
                $file->storeAs("nas/public/dokumen-lain/$store->id/file", $filename);

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
        $data = DokumenLain::query()
            ->where('id', '=', $id)
            ->firstOrFail();
        ListResource::withoutWrapping();

        return new ListResource($data);
    }

    public function update(Request $request, $id)
    {
        $rules = [
            'title' => 'required|string',
            'tipe' => 'required|string',
            'private' => 'required|boolean',
            'file' => "sometimes|nullable|max:21000|mimes:doc,docx,ppt,pptx,xls,xlsx,pdf",
            'url' => 'required_if:tipe,url|nullable|string|active_url',
        ];

        $messages = [
            'title.required' => __('validation.required', ['attribute' => 'Title']),
            'tipe.required' => __('validation.required', ['attribute' => 'Tipe']),
            'private.required' => __('validation.required', ['attribute' => 'Private']),
            'file.required' => __('validation.required', ['attribute' => 'File']),
            'file.max' => __('validation.max.file', ['attribute' => 'File', 'max' => '20', 'type' => 'MB']),
            'file.mimes' => __('validation.mimes', ['attribute' => 'File', 'values' => 'DOCX, PPTX, XSLX, PDF']),
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
            $update = DokumenLain::query()->where('id', '=', $id)->firstOrFail();
            $update->user_id = $request->user()->id;
            $update->title = $request->get('title');
            $update->tipe = $request->get('tipe');
            $update->private = (bool) $request->get('private');


            if ($request->hasFile('file') && $request->get('tipe') === 'file') {
                $oldfileexists = Storage::disk('publicNas')->exists("dokumen-lain/$update->id/file/$update->file");
                if ($oldfileexists) {
                    Storage::disk('publicNas')->delete("dokumen-lain/$update->id/file/$update->file");
                }
                $file = $request->file('file');
                $filename =  Str::slug(Str::lower($update->title)) . '.' . $file->getClientOriginalExtension();
                $file->storeAs("nas/public/dokumen-lain/$update->id/file", $filename);

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

            DokumenLain::query()->whereIn('id', $ids_to_delete)->delete();
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

    public function listDokumen()
    {
        $fieldName = array(
            'created_at' => 'created_at'
        );
        $per_page = !request()->filled('per_page') ? 10 : (int) request('per_page');
        $direction = request()->query('direction');
        $sortBy = request()->query('sortBy');

        $query = DokumenLain::query();
        $query->where('private', false);

        $lists = $query->orderBy($fieldName[$sortBy], $direction)->paginate($per_page);

        return new ApiListResources($lists);
    }

    public function getFile($slug)
    {
        $query = DokumenLain::query();
        $query->where('slug', $slug);
        $data = $query->firstOrFail();

        $oldfileexists = Storage::disk('publicNas')->exists("dokumen-lain/$data->id/file/$data->file");
        if ($oldfileexists && $data->tipe === 'file') {
            return Storage::disk('publicNas')->response("dokumen-lain/$data->id/file/$data->file");
        } else {
            abort(404);
        }
    }
}
