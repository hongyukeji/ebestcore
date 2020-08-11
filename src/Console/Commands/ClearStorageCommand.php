<?php

namespace System\Console\Commands;

use RuntimeException;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class ClearStorageCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'clear:storage';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear all compiled storage files';

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
        $path = realpath(storage_path('app/public'));

        if (!$path) {
            throw new RuntimeException('Storage path not found.');
        }

        foreach ($this->files->glob("{$path}/*") as $view) {
            $this->files->delete($view);
            $this->files->deleteDirectory($view);
        }

        //$this->files->cleanDirectory($path);
        //$this->files->put($path . '/.gitignore', "*\n!.gitignore\n");

        //$this->call('storage:staticLink');

        $this->info('Compiled storage cleared.');
    }
}
