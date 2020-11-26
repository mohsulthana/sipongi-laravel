<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\Handler as Exception;
use App\Http\Controllers\Controller;
use App\Models\HotspotDaily;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HotspotDailyController extends Controller
{
    public function getGrafikKumulatif()
    {
        $from = !request()->filled('from') ? Carbon::now()->subYears(1)->format('Y') : (int) request('from');
        $to = !request()->filled('to') ? Carbon::now()->format('Y') : (int) request('to');
        $satelit = !request()->filled('satelit') ? ['LPN-MODIS', 'LPN-NPP', 'LPN-NOAA20', 'LPN-LANDSAT8'] : request('satelit');

        $res = array(
            'yearNow' => $this->grafikKumulatif($to, $satelit),
            'yearBefore' => $this->grafikKumulatif($from, $satelit)
        );

        return response()->json($res);
    }

    private function grafikKumulatif($year, $satelit)
    {
        $column = '';
        for ($i = 1; $i <= 31; $i++) {
            $column .= $i < 31 ? "t$i+" : "t$i";
        }
        $from = Carbon::create($year)->startOfYear();
        $to = Carbon::create($year)->endOfYear();

        $query1 = HotspotDaily::query();
        $query1->select([
            DB::raw("($column) as value"),
            'id',
            'bulan'
        ]);
        $query1->whereBetween('bulan', [$from->copy()->format('Ym'), $to->copy()->format('Ym')]);
        $query1->whereIn('sumber', $satelit);
        $query1->whereNotNull('provinsi_id');
        $query1->orderBy('bulan');

        $query = HotspotDaily::query();
        $query->select([
            DB::raw("sum(a.value) as value"),
            DB::raw("TO_DATE(CAST(hotspot_daily.bulan AS text), 'YYYYMMM') as date"),
        ]);
        $query->joinSub($query1, 'a', function ($join) {
            $join->on('hotspot_daily.id', '=', 'a.id');
        });
        $query->whereBetween('hotspot_daily.bulan', [$from->copy()->format('Ym'), $to->copy()->format('Ym')]);
        $query->whereIn('hotspot_daily.sumber', $satelit);
        $query->whereNotNull('hotspot_daily.provinsi_id');
        $query->orderBy('hotspot_daily.bulan');
        $query->groupBy(['hotspot_daily.bulan']);

        $lists = $query->get();

        $res = collect(CarbonPeriod::create($from->copy()->format('Y-m-d'), "1 month", $to->copy()->format('Y-m-d')))
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
