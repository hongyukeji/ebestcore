<?php

namespace System\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class SyncDemoCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:demo {--force}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync demo database';

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
        $this->line("Start sync demo ...");

        (new \System\Services\AdvertService())->batchImport(config('demo.adverts', []));
        (new \System\Services\LinkService())->batchImport(config('demo.links', []));
        (new \System\Services\NavigationService())->batchImport(config('demo.navigations', []));
        (new \System\Services\SliderService())->batchImport(config('demo.sliders', []));
        (new \System\Services\ArticleCategoryService())->batchImport(config('demo.article_categories', []));

        $this->info("Demo sync success.");

        return true;
    }

}
