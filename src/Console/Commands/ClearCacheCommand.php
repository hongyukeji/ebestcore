<?php

namespace System\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class ClearCacheCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'clear:cache {--force}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear all compiled cache all files';

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
        $this->line("Start clear cache command...");

        Artisan::call("clear-compiled");

        Artisan::call("auth:clear-resets");
        $this->info('Expired reset tokens cleared.');

        Artisan::call("cache:clear");
        $this->info('Application cache cleared.');

        Artisan::call("config:clear");
        $this->info('Configuration cache cleared.');

        Artisan::call("route:clear");
        $this->info('Route cache cleared.');

        Artisan::call("view:clear");
        $this->info('Compiled views cleared.');

        Artisan::call("clear:session");
        $this->info('Session cache cleared.');

        Artisan::call("clear:bootstrap");
        $this->info('Bootstrap cache cleared.');

        Artisan::call("clear:log");
        $this->info('Log cache cleared.');

        if (class_exists('Barryvdh\\Debugbar\\Console\\ClearCommand')) {
            Artisan::call("debugbar:clear");
            $this->info('Debugbar Storage cleared.');
        }

        if ($this->option('force')) {
            Artisan::call("clear:storage");
            $this->info('Storage cache cleared.');
        }

        /*if ($this->confirm('Execute clear cache?', true)) {

            $this->clearCache();

            if ($this->confirm('Clear storage?', false)) {
                Artisan::call("clear:storage");
                $this->info('Storage cache cleared.');
            } else {
                $this->warn("Cancel Clear storage command");
            }

        } else {
            $this->warn("Cancel clear cache command");
        }*/
        $this->info("Clear cache success.");
    }
}
