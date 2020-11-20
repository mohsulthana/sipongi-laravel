<?php

namespace App\Console\Commands;

use App\Jobs\ImportDataLama\LevelHotspotSatelit;
use Illuminate\Console\Command;

class NewSopHotspot extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'new:sop';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        dispatch((new LevelHotspotSatelit())->onQueue('migrasi'));
    }
}
