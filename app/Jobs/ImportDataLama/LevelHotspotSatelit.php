<?php

namespace App\Jobs\ImportDataLama;

use App\Jobs\ImportDataLama\Chunk\ChunkLevelHotspotSatelit;
use App\Models\HotspotSatelit;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class LevelHotspotSatelit implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        DB::table('hotspot_satelit')
            ->where(DB::raw("date(date_hotspot)"), '>', '2020-08-13')
            ->where('source', 'lapan')
            ->where('confidence', 7)
            ->update([
                'confidence_level' =>  'low'
            ]);
        DB::table('hotspot_satelit')
            ->where(DB::raw("date(date_hotspot)"), '>', '2020-08-13')
            ->where('source', 'lapan')
            ->where('confidence', 8)
            ->update([
                'confidence_level' =>  'medium'
            ]);
        DB::table('hotspot_satelit')
            ->where(DB::raw("date(date_hotspot)"), '>', '2020-08-13')
            ->where('source', 'lapan')
            ->where('confidence', 9)
            ->update([
                'confidence_level' =>  'high'
            ]);

        DB::table('hotspot_satelit')
            ->where(DB::raw("date(date_hotspot)"), '>', '2020-01-01')
            ->where(DB::raw("date(date_hotspot)"), '<', '2020-08-14')
            ->where('sumber', 'LPN-MODIS')
            ->where('source', 'lapan')
            ->where('confidence', '<=', 29)
            ->update([
                'confidence_level' =>  'low'
            ]);
        DB::table('hotspot_satelit')
            ->where(DB::raw("date(date_hotspot)"), '>', '2020-01-01')
            ->where(DB::raw("date(date_hotspot)"), '<', '2020-08-14')
            ->where('sumber', 'LPN-MODIS')
            ->where('source', 'lapan')
            ->where('confidence', '>=', 30)
            ->where('confidence', '<=', 80)
            ->update([
                'confidence_level' =>  'medium'
            ]);
        DB::table('hotspot_satelit')
            ->where(DB::raw("date(date_hotspot)"), '>', '2020-01-01')
            ->where(DB::raw("date(date_hotspot)"), '<', '2020-08-14')
            ->where('sumber', 'LPN-MODIS')
            ->where('source', 'lapan')
            ->where('confidence', '>=', 81)
            ->update([
                'confidence_level' =>  'high'
            ]);

        DB::table('hotspot_satelit')
            ->where(DB::raw("date(date_hotspot)"), '>', '2020-01-01')
            ->where(DB::raw("date(date_hotspot)"), '<', '2020-08-14')
            ->where('sumber', '<>', 'LPN-MODIS')
            ->where('source', 'lapan')
            ->where('confidence', 7)
            ->update([
                'confidence_level' =>  'low'
            ]);
        DB::table('hotspot_satelit')
            ->where(DB::raw("date(date_hotspot)"), '>', '2020-01-01')
            ->where(DB::raw("date(date_hotspot)"), '<', '2020-08-14')
            ->where('sumber', '<>', 'LPN-MODIS')
            ->where('source', 'lapan')
            ->where('confidence', 8)
            ->update([
                'confidence_level' =>  'medium'
            ]);
        DB::table('hotspot_satelit')
            ->where(DB::raw("date(date_hotspot)"), '>', '2020-01-01')
            ->where(DB::raw("date(date_hotspot)"), '<', '2020-08-14')
            ->where('sumber', '<>', 'LPN-MODIS')
            ->where('source', 'lapan')
            ->where('confidence', 9)
            ->update([
                'confidence_level' =>  'high'
            ]);

        DB::table('hotspot_satelit')
            ->where(DB::raw("date(date_hotspot)"), '>', '2020-01-01')
            ->where('source', 'nasa/modis')
            ->where('confidence', '<=', 29)
            ->update([
                'confidence_level' =>  'low'
            ]);
        DB::table('hotspot_satelit')
            ->where(DB::raw("date(date_hotspot)"), '>', '2020-01-01')
            ->where('source', 'nasa/modis')
            ->where('confidence', '>=', 30)
            ->where('confidence', '<=', 80)
            ->update([
                'confidence_level' =>  'medium'
            ]);
        DB::table('hotspot_satelit')
            ->where(DB::raw("date(date_hotspot)"), '>', '2020-01-01')
            ->where('source', 'nasa/modis')
            ->where('confidence', '>=', 81)
            ->update([
                'confidence_level' =>  'high'
            ]);

        DB::table('hotspot_satelit')
            ->where(DB::raw("date(date_hotspot)"), '<', '2020-01-01')
            ->where('confidence', '<=', 29)
            ->update([
                'confidence_level' =>  'low'
            ]);
        DB::table('hotspot_satelit')
            ->where(DB::raw("date(date_hotspot)"), '<', '2020-01-01')
            ->where('confidence', '>=', 30)
            ->where('confidence', '<=', 80)
            ->update([
                'confidence_level' =>  'medium'
            ]);
        DB::table('hotspot_satelit')
            ->where(DB::raw("date(date_hotspot)"), '<', '2020-01-01')
            ->where('confidence', '>=', 81)
            ->update([
                'confidence_level' =>  'high'
            ]);
    }

    public function tags()
    {
        return ['migrasi', 'migrasi:hotspot-satelit-new-sop', 'hotspot-satelit-new-sop:get-data'];
    }
}
