<?php

namespace System\Console\Commands;

use Illuminate\Console\Command;

class ClearSystemCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'clear:system';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear all compiled system files';

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
        $this->line("Start clear system command...");

        $paths = config('systems.files', []);

        foreach ($paths as $path) {
            if (\Illuminate\Support\Facades\Storage::disk('root')->exists($path)) {
                \Illuminate\Support\Facades\Storage::disk('root')->deleteDirectory($path);
            }
        }

        $this->info('Compiled system cleared.');
    }
}
