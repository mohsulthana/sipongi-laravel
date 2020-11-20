<?php

namespace App\Jobs\ReadHotspot;

use App\Models\HotspotDaily;
use App\Models\HotspotSatelit;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class UpdateResumeHotspot implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $source;
    private $setting;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($source, $setting)
    {
        $this->source = $source;
        $this->setting = $setting;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $setting = $this->setting;
        $dates = HotspotSatelit::query()
            ->select([
                DB::raw("date(max(date_hotspot)) as date_hotspot"),
            ])
            ->where('confidence_level', 'high')
            ->where('source', 'like', "$this->source%")
            ->where('hotspot_daily_update', false)
            ->whereNotNull('provinsi_id')
            ->groupByRaw('date(date_hotspot)')
            ->get();
        if ($dates->count() > 0) {
            foreach ($dates as $date) {
                $datas = HotspotSatelit::query()
                    ->select([
                        'sumber',
                        'provinsi_id',
                        DB::raw("count(*) as csatelit")
                    ])
                    ->where('confidence_level', 'high')
                    ->where('source', 'like', "$this->source%")
                    ->whereDate('date_hotspot', Carbon::parse($date->date_hotspot)->format('Y-m-d'))
                    ->whereNotNull('provinsi_id')
                    ->groupByRaw('sumber,provinsi_id')
                    ->get();

                if ($datas->count() > 0) {
                    foreach ($datas as $data) {
                        $bulan = Carbon::parse($date->date_hotspot)->format('Ym');
                        $day = Carbon::parse($date->date_hotspot)->format('j');
                        $days = "t$day";
                        $update = HotspotDaily::query()
                            ->where('bulan', $bulan)
                            ->where('provinsi_id', $data->provinsi_id)
                            ->where('sumber', $data->sumber)->first();

                        if (!$update) {
                            $update = new HotspotDaily;
                            $update->bulan = $bulan;
                            $update->provinsi_id = $data->provinsi_id;
                            $update->sumber = $data->sumber;
                        }
                        $update->{$days} = $data->csatelit;
                        $update->save();
                    }
                }
            }
        }

        HotspotSatelit::query()
            ->where('source', 'like', "$this->source%")
            ->where('hotspot_daily_update', false)
            ->update([
                'hotspot_daily_update' => true
            ]);
    }

    public function tags()
    {
        return ['update-resume-hotspot', 'update-resume-hotspot:daily'];
    }
}
