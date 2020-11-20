<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Exceptions\Handler as Exception;
use App\Http\Resources\Disclaimer\ListResource;
use App\Http\Resources\Disclaimer\ListResources;
use App\Models\Disclaimer;

class DisclaimerController extends Controller
{
    public function getDataApi()
    {
        $data = Disclaimer::query()
            ->where('active', '=', true)
            ->firstOrFail();
        
        ListResource::withoutWrapping();

        return new ListResource($data);
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

        $query = Disclaimer::query();

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
            'date'   => 'required|date',
            'text'   => 'required|string',
            'active' => 'required|boolean',
        ];
        $this->validate($request, $rules);

        //Store data
        DB::beginTransaction();
        try {
            $store = Disclaimer::create([
                'date'   => $request->date,
                'text'   => $request->text,
                'active' => $request->active,
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
        $data = Disclaimer::query()
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
            'date'   => 'required|date',
            'text'   => 'required|string',
            'active' => 'required|boolean',
        ];
        $this->validate($request, $rules);

        //Update data
        DB::beginTransaction();
        try {
            $update = Disclaimer::query()->where('id', '=', $id)->firstOrFail();
            $update->date = $request->date;
            $update->text = $request->text;
            $update->active = $request->active;
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

            Disclaimer::query()->whereIn('id', $ids_to_delete)->delete();
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
