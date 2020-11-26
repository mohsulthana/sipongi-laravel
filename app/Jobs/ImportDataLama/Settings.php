<?php

namespace App\Jobs\ImportDataLama;

use App\Models\Settings as AppSettings;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class Settings implements ShouldQueue
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
        $settings =  DB::connection('pgsql_hotspot')->table('sosmed_state')->get();
        DB::delete('truncate settings');
        $data = array();
        foreach ($settings as $setting) {
            $data[] = array(
                'account' => $setting->account,
                'position' => $setting->position,
                'running' => $setting->running,
                'config' => $setting->config
            );
        }
        DB::table('settings')->insertOrIgnore($data);
    }

    public function tags()
    {
        return ['migrasi', 'migrasi:sosmed_state', 'sosmed_state:import-data'];
    }
}
