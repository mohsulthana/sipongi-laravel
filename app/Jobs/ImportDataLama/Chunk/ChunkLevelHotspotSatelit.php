<?php

namespace App\Jobs\ImportDataLama\Chunk;

use App\Models\HotspotSatelit;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ChunkLevelHotspotSatelit implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $datas;
    private $tipe;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($datas, $tipe)
    {
        $this->datas = $datas;
        $this->tipe = $tipe;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        activity()->disableLogging();
        foreach ($this->datas as $val) {
            $level = 'low';
            if ($this->tipe === 1) {
                if ($val->confidence <= 29) {
                    $level = 'low';
                } else if ($val->confidence >= 30 && $val->confidence <= 80) {
                    $level = 'medium';
                } else if ($val->confidence >= 81) {
                    $level = 'high';
                }
            }

            if ($this->tipe === 2) {
                if ($val->confidence === 7) {
                    $level = 'low';
                } else if ($val->confidence === 8) {
                    $level = 'medium';
                } else if ($val->confidence === 9) {
                    $level = 'high';
                }
            }

            HotspotSatelit::query()
                ->where('id', $val->id)
                ->update([
                    'confidence_level' =>  $level
                ]);
        }
        activity()->enableLogging();
    }

    public function tags()
    {
        return ['migrasi', 'migrasi:hotspot-satelit-new-sop', 'hotspot-satelit-new-sop:insert-data'];
    }
}
