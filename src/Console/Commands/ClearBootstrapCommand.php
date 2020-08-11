<?php

namespace System\Console\Commands;

use RuntimeException;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class ClearBootstrapCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'clear:bootstrap';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear all compiled bootstrap files';

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
        $path = realpath(base_path('bootstrap/cache'));

        if (!$path) {
            throw new RuntimeException('Bootstrap path not found.');
        }

        foreach ($this->files->glob("{$path}/*") as $view) {
            $this->files->delete($view);
        }

        $this->info('Compiled bootstrap cleared.');
    }
}
