<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class StorageNasLink extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'nas:link';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a symbolic link NAS';

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
        if (file_exists(storage_path('app/lapan'))) {
            $this->info('The "app/lapan" directory already exists.');
        } else {
            $pathLapan = $this->ask('Path folder data csv lapan?');
            if (file_exists($pathLapan)) {
                $this->laravel->make('files')->link(
                    $pathLapan,
                    storage_path('app/lapan')
                );

                $this->info("The [app/lapan] directory has been linked.");
            } else {
                $this->error("The [$pathLapan] directory not found.");
            }
        }

        if (file_exists(storage_path('app/nas'))) {
            $this->info('The "app/nas" directory already exists.');
            if (file_exists(public_path('storage'))) {
                $this->info('The "public/storage" directory already exists.');
            } else {
                $this->laravel->make('files')->link(
                    storage_path('app/nas/public'),
                    public_path('storage')
                );

                $this->info('The [public/storage] directory has been linked.');
            }
        } else {
            $pathNas = $this->ask('Path folder qnap-nas?');
            if (file_exists($pathNas)) {
                $this->laravel->make('files')->link(
                    $pathNas,
                    storage_path('app/nas')
                );

                $this->info("The [app/nas] directory has been linked.");

                if (file_exists(public_path('storage'))) {
                    $this->info('The "public/storage" directory already exists.');
                } else {
                    $this->laravel->make('files')->link(
                        storage_path('app/nas/public'),
                        public_path('storage')
                    );

                    $this->info('The [public/storage] directory has been linked.');
                }
            } else {
                $this->error("The [$pathNas] directory not found.");
            }
        }

        if (file_exists(public_path('storages'))) {
            $this->info('The "public/storages" directory already exists.');
        } else {
            $this->laravel->make('files')->link(
                storage_path('app/public'),
                public_path('storages')
            );

            $this->info('The [public/storages] directory has been linked.');
        }
    }
}
