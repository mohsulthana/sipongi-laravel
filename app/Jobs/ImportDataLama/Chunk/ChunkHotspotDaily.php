<?php

namespace App\Jobs\ImportDataLama\Chunk;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ChunkHotspotDaily implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $datas;
    private $dataProvs;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($datas, $dataProvs)
    {
        $this->datas = $datas;
        $this->dataProvs = $dataProvs;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        activity()->disableLogging();
        $data = array();
        foreach ($this->datas as $val) {
            $checkProv = $this->dataProvs->where('old_id', $val->provinsi_id)->first();
            $provId = $checkProv ? $checkProv->id : null;

            $data[] = array(
                'id' => Str::orderedUuid(),
                'provinsi_id' => $provId,
                'bulan' => $val->bulan,
                'sumber' => $val->sumber,
                't1' => $val->t1,
                't2' => $val->t2,
                't3' => $val->t3,
                't4' => $val->t4,
                't5' => $val->t5,
                't6' => $val->t6,
                't7' => $val->t7,
                't8' => $val->t8,
                't9' => $val->t9,
                't10' => $val->t10,
                't11' => $val->t11,
                't12' => $val->t12,
                't13' => $val->t13,
                't14' => $val->t14,
                't15' => $val->t15,
                't16' => $val->t16,
                't17' => $val->t17,
                't18' => $val->t18,
                't19' => $val->t19,
                't20' => $val->t20,
                't21' => $val->t21,
                't22' => $val->t22,
                't23' => $val->t23,
                't24' => $val->t24,
                't25' => $val->t25,
                't26' => $val->t26,
                't27' => $val->t27,
                't28' => $val->t28,
                't29' => $val->t29,
                't30' => $val->t30,
                't31' => $val->t31,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            );
        }
        DB::table('hotspot_daily')->insertOrIgnore($data);
        activity()->enableLogging();
    }

    public function tags()
    {
        return ['migrasi', 'migrasi:hotspot-daily', 'hotspot-daily:insert-data'];
    }
}
