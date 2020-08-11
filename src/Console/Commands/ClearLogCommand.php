<?php

namespace System\Console\Commands;

use RuntimeException;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class ClearLogCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'clear:log';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear all compiled log files';

    /**
     * The filesystem instance.
     *
     * @var \Illuminate\Filesystem\Filesystem
     */
    protected $files;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(Filesystem $files)
    {
        parent::__construct();

        $this->files = $files;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $path = realpath(storage_path('logs'));

        if (!$path) {
            throw new RuntimeException('Log path not found.');
        }

        foreach ($this->files->glob("{$path}/*") as $view) {
            $this->files->delete($view);
            $this->files->deleteDirectory($view);
        }

        $this->info('Compiled log cleared.');
    }
}
