<?php

namespace System\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class DbBackupCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:backup {file_name?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Backup the database';

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
        // TODO: mysqldump Windows path.
        $file_name = $this->argument('file_name') ?: 'db_backup_' . date("Y-m-d_H-i-s") . '_' . uuid();
        $file_dir = storage_path('app/backup/database');
        if (!is_dir($file_dir)) {
            @mkdir($file_dir, 0755, true);
        }
        $sql_path_name = $file_dir . DIRECTORY_SEPARATOR . $file_name . '.sql';
        $process = new Process(sprintf(
            'mysqldump -u%s -p%s %s > %s',
            config('database.connections.mysql.username'),
            config('database.connections.mysql.password'),
            config('database.connections.mysql.database'),
            $sql_path_name
        ));
        try {
            $process->mustRun();
            $this->info('数据库备份成功！文件位置：' . $sql_path_name);
        } catch (ProcessFailedException $exception) {
            $this->error('数据库备份失败！错误信息：' . $exception->getMessage());
        }
    }
}