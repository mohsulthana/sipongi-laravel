<?php

namespace App\Jobs\ReadHotspot;

use App\Imports\ReadHotspot\ImportHotspotModis;
use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class HotspotModis implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $setting;
    private $import;
    private $fileName;
    /**
     * Create a new job instance.
     *
     * @param \Laravel\Horizon\Contracts\TagRepository $tagRepository
     * @return void
     */
    public function __construct($setting)
    {
        $this->setting = $setting;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $modis_source = array(
            'https://firms.modaps.eosdis.nasa.gov/active_fire/text/SouthEast_Asia_48h.csv',
            'https://firms.modaps.eosdis.nasa.gov/active_fire/c6/text/MODIS_C6_SouthEast_Asia_24h.csv'
        );

        $exists = Storage::disk('privateNas')->exists('download/hotspot/modis');
        if (!$exists) {
            Storage::disk('privateNas')->makeDirectory('download/hotspot/modis');
        }

        $dieUrl = 0;
        foreach ($modis_source as $val) {
            try {
                $http = new Client;
                $uri = $val;
                $this->fileName = Carbon::now()->format('Ymd_His') . '.csv';

                $res = $http->get($uri);
                if ($res->getStatusCode() === 200) {
                    $path = Storage::disk('privateNas')->path("download/hotspot/modis/$this->fileName");
                    $res2 = $http->request('GET', $uri, [
                        'sink' => $path
                    ]);
                    if ($res2->getStatusCode() === 200) {
                        $exists = Storage::disk('privateNas')->exists("download/hotspot/modis/$this->fileName");
                        if ($exists) {
                            (new ImportHotspotModis($this->fileName, $val, $this->setting))->import($path);
                        }
                    } else {
                        $dieUrl++;
                    }
                } else {
                    $dieUrl++;
                }
            } catch (ClientException $e) {
                $dieUrl++;
            }
        }

        if ($dieUrl === count($modis_source)) {
            abort(404, 'Server nasa modis mati.');
        }

        // $path = Storage::disk('privateNas')->path("download/hotspot/modis/20200803_003557.csv");
        // (new ImportHotspotModis('20200803_003557.csv', 'tes', $this->setting))->import($path);
    }

    public function tags()
    {
        return ['read-hotspot', 'read-hotspot:modis', 'modis:import-data'];
    }

    public function failed(Exception $exception)
    {
        $exists = Storage::disk('privateNas')->exists("download/hotspot/modis/$this->fileName");
        if ($exists) {
            Storage::disk('privateNas')->delete("download/hotspot/modis/$this->fileName");
        }
    }
}
