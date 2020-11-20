<?php

namespace App\Jobs\ReadHotspot;

use App\Imports\ReadHotspot\ImportHotspotLapan;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class HotspotLapan implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $setting;
    private $date;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($setting, $date)
    {
        $this->setting = $setting;
        $this->date = $date;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $updateResume = false;
        $fileDt = $this->date->format('Ymd');
        $allFiles = Storage::disk('lapan')->allFiles();
        $files = preg_grep("/$fileDt.*\.csv$/i", $allFiles);
        foreach ($files as $file) {
            $exists = Storage::disk('lapan')->exists($file);
            if ($exists) {
                $updateResume = true;
                $path = Storage::disk('lapan')->path($file);
                (new ImportHotspotLapan($this->setting))->import($path);
            }
        }
        if ($updateResume) {
            dispatch((new UpdateResumeHotspot('lapan', $this->setting))->onQueue('hotspot'))->delay(now()->addMinutes(1));
        }
    }

    public function tags()
    {
        return ['read-hotspot', 'read-hotspot:lapan', 'lapan:import-data'];
    }
}
