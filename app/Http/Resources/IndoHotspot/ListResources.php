<?php

namespace App\Http\Resources\IndoHotspot;

use App\Traits\HotspotSatelitTrait;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Str;

class ListResources extends ResourceCollection
{
    use HotspotSatelitTrait;
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'type' => 'FeatureCollection',
            'features' => ListResource::collection($this->collection),
            'kabkota' => $this->collection->groupBy(['kabkota', 'sumber'])->sortBy('prov_id')->sortBy('kotakab_id')->map(function ($item, $key) {
                return collect($item)->map(function ($item2, $key2) {
                    return [
                        'count' => collect($item2)->count(),
                        'data' => [
                            'latcen' => $item2[0]->latcen,
                            'longcen' => $item2[0]->longcen,
                            'sumber' => $item2[0]->sumber,
                            'ori_sumber' => $this->hotspot_name($item2[0]->sumber2, true),
                            'kotakab_id' => $item2[0]->kotakab_id,
                            'kabkota' => Str::title($item2[0]->kabkota),
                            'nama_provinsi' => $item2[0]->provinsi ? Str::title($item2[0]->provinsi_rel->nama_provinsi) : null,
                        ],
                    ];
                });
            }),
            'totals' => ListResource::collection($this->collection)->collection->groupBy(['sumber'])->map(function ($item, $key) {
                return collect($item)->count();
            }),
            'totalsLevel' => ListResource::collection($this->collection)->collection->groupBy(['sumber', 'confidence_level'])->map(function ($item, $key) {
                return collect($item)->map(function ($item2, $key2) {
                    return collect($item2)->count();
                });
            })
        ];
    }
}
