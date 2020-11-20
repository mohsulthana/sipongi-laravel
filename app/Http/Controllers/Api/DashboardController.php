<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\HotspotSatelit;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    private function totalHotspot($sumber)
    {
        $query2 = HotspotSatelit::query();
        $query2->where('hotspot_satelit.publikasi', true);
        $query2->where('hotspot_satelit.sumber', $sumber);
        $query2->orderBy('hotspot_satelit.date_hotspot', 'desc');
        $lastData = $query2->first();
        if($lastData==null)
            return 0;
        $formDate = $lastData->date_hotspot;

        $query = HotspotSatelit::query();
        $query->select([
            'id'
        ]);
        $query->whereDate('date_hotspot', $formDate->format('Y-m-d'));
        $query->where('publikasi', true);
        $query->where('sumber', $sumber);
        $query->whereIn('confidence_level', ['high']);
        $query->whereNotNull('provinsi_id');
        $query->whereNotNull('kotakab_id');

        $count = $query->count();

        return $count;
    }

    public function getDashboardData()
    {
        $res = array(
            'totalNasaModis' => $this->totalHotspot('NASA-MODIS'),
            'totalLpnModis' => $this->totalHotspot('LPN-MODIS'),
            'totalLpnSnpp' => $this->totalHotspot('LPN-NPP'),
            'totalLpnNoaa' => $this->totalHotspot('LPN-NOAA20'),
            'totalLpnLandsat' => $this->totalHotspot('LPN-LANDSAT8')
        );

        return response()->json($res, 200);
    }
}
