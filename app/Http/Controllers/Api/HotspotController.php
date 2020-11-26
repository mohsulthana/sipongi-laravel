<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\Handler as Exception;
use App\Http\Controllers\Controller;
use App\Http\Resources\IndoHotspot\ListResources;
use App\Models\HotspotSatelit;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HotspotController extends Controller
{
    public function getIndoHotspot()
    {
        $late = !request()->filled('late') ? 12 : (int) request('late');
        $confidence = !request()->filled('confidence') ? [] : request('confidence');

        $query2 = HotspotSatelit::query();
        $query2->where('hotspot_satelit.publikasi', true);
        $query2->where('hotspot_satelit.source', 'lapan');
        $query2->orderBy('hotspot_satelit.date_hotspot', 'desc');
        $lastData = $query2->first();

        $formDate = $lastData->date_hotspot->subHours($late);

        $lists = [];
        if (count($confidence) > 0) {
            $query1 = HotspotSatelit::query();
            $query1->select([
                'hotspot_satelit.sumber',
                'hotspot_satelit.kotakab_id',
                DB::raw("ST_Y(ST_Centroid(ST_Union(geom))) as lat"),
                DB::raw("ST_X(ST_Centroid(ST_Union(geom))) as long")
            ]);
            $query1->where('hotspot_satelit.date_hotspot', '>=', $formDate);
            $query1->where('hotspot_satelit.publikasi', true);
            $query1->where('hotspot_satelit.source', 'lapan');
            $query1->whereIn('hotspot_satelit.confidence_level', $confidence);
            $query1->whereNotNull('hotspot_satelit.provinsi_id');
            $query1->whereNotNull('hotspot_satelit.kotakab_id');
            $query1->groupBy(['hotspot_satelit.sumber', 'hotspot_satelit.kotakab_id']);

            $query = HotspotSatelit::query();
            $query->select([
                'hotspot_satelit.*',
                DB::raw("a.lat as latcen"),
                DB::raw("a.long as longcen")
            ]);
            $query->joinSub($query1, 'a', function ($join) {
                $join->on('hotspot_satelit.sumber', '=', 'a.sumber')
                    ->on('hotspot_satelit.kotakab_id', '=', 'a.kotakab_id');
            });
            $query->with([
                'provinsi_rel' => function ($q) {
                    $q->select(['id', 'nama_provinsi', 'pulau']);
                }
            ]);
            $query->where('hotspot_satelit.date_hotspot', '>=', $formDate);
            $query->where('hotspot_satelit.publikasi', true);
            $query->where('hotspot_satelit.source', 'lapan');
            $query->whereIn('hotspot_satelit.confidence_level', $confidence);
            $query->whereNotNull('hotspot_satelit.provinsi_id');
            $query->whereNotNull('hotspot_satelit.kotakab_id');
            $query->orderBy('hotspot_satelit.provinsi_id');
            $query->orderBy('hotspot_satelit.kotakab_id');

            $lists = $query->get();
        }

        return new ListResources($lists);
    }

    public function getTotalHotspot()
    {
        $late = !request()->filled('day') ? 14 : (int) request('day');
        $confidence = !request()->filled('confidence') ? [] : request('confidence');
        $formDate = Carbon::now()->subDays($late);

        $query = HotspotSatelit::query();
        $query->select([
            'id'
        ]);
        $query->where('date_hotspot', '>=', $formDate);
        $query->where('publikasi', true);
        $query->where('source', 'lapan');
        $query->whereIn('confidence_level', $confidence);
        $query->whereNotNull('provinsi_id');
        $query->whereNotNull('kotakab_id');

        $count = $query->count();

        return response()->json($count);
    }

    public function getTotalHotspotProv()
    {
        $late = !request()->filled('day') ? 14 : (int) request('day');
        $confidence = !request()->filled('confidence') ? [] : request('confidence');
        $formDate = Carbon::now()->subDays($late);

        $query = HotspotSatelit::query();
        $query->select([
            'id'
        ]);
        $query->where('date_hotspot', '>=', $formDate);
        $query->where('publikasi', true);
        $query->where('source', 'lapan');
        $query->whereIn('confidence_level', $confidence);
        $query->whereNotNull('provinsi_id');
        $query->whereNotNull('kotakab_id');
        $query->groupBy(['provinsi_id']);

        $count = $query->count();

        return response()->json($count);
    }

    public function getGrafikMingguan()
    {
        $now = Carbon::now();
        $startWeek = Carbon::now()->startOfWeek();
        $diff = $startWeek->diffInDays($now);
        $startWeekBfr = Carbon::now()->subWeeks(1)->startOfWeek();
        $endWeekBfr = $startWeekBfr->copy()->addDays($diff);
        $confidence = !request()->filled('confidence') ? [] : request('confidence');

        $res = array(
            'weekNow' => $this->grafikMingguan($startWeek, $now, $confidence),
            'weekBefore' => $this->grafikMingguan($startWeekBfr, $endWeekBfr, $confidence)
        );

        return response()->json($res);
    }

    private function grafikMingguan($startDate, $endDate, $confidence)
    {
        $query = HotspotSatelit::query();
        $query->select([
            DB::raw("count(id) as value"),
            DB::raw("date(date_hotspot) as date")
        ]);
        $query->whereBetween(DB::raw("date(date_hotspot)"), [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')]);
        $query->where('publikasi', true);
        $query->where('source', 'lapan');
        $query->whereIn('confidence_level', $confidence);
        $query->whereNotNull('provinsi_id');
        $query->whereNotNull('kotakab_id');
        $query->orderBy(DB::raw("date(date_hotspot)"));
        $query->groupBy([DB::raw("date(date_hotspot)")]);

        $lists = $query->get();

        $res = collect(CarbonPeriod::create($startDate, $endDate))
            ->map(function ($date) {
                return [
                    'value' => 0,
                    'date' => $date->format('Y-m-d')
                ];
            })
            ->keyBy('date')
            ->merge(
                $lists->keyBy('date')
            )
            ->sortKeys()
            ->pluck('value');

        return $res;
    }
}
