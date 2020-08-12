<?php

namespace System\Console\Commands;

use Illuminate\Console\Command;

class ComposerAutoloadCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'composer:autoload';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Composer autoload';

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
        $this->info('Composer run start...');

        $php = php_run_path();  // 带路径运行PHP命令：/www/server/php/72/bin/php composer.phar dump-autoload
        $path = base_path();    // 系统根路径
        $cmd = "cd {$path} && {$php} composer.phar install --prefer-dist --no-progress && {$php} composer.phar dump-autoload";
        if (!strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            //$cmd = 'sudo ' . $cmd;
        }
        if (function_exists('popen')) {
            pclose(popen($cmd, "r"));
        } elseif (function_exists('exec')) {
            exec($cmd);
        }

        $this->info('Composer run success!');
    }
}
