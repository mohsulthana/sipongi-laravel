<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Arcanedev\LogViewer\Contracts\LogViewer as LogViewerContract;
use Carbon\Carbon;

class LogViewer extends Command
{
    protected $logViewer;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'clear:logs {--days= : (optional) Records older than this number of days will be cleaned.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear all generated log files';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(LogViewerContract $logViewer)
    {
        parent::__construct();
        $this->logViewer = $logViewer;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        try {
            $this->comment('Cleaning system logs...');

            $maxAgeInDays = $this->option('days') ?? 3;

            $expired = Carbon::now()->subDays($maxAgeInDays)->format('Y-m-d');
            $this->logViewer->delete($expired);
            $this->info('Successfully cleared the logs!');
        } catch (\Exception $e) {
            $this->info('File log not found!');
        }
    }
}
