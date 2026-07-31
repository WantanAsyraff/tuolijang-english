<?php

declare(strict_types=1);


namespace App\Console\Commands;

use Illuminate\Console\Command;

class UpV16Command extends Command
{
    /**
     * @var string
     */
    protected $signature = 'tl:usr';

    /**
     * Create a new command instance.
     */
    public function __construct()
    {
        parent::__construct();
    }

    public function handle(): void
    {
        if ($this->confirm('此命令用于处理1.6版本用户信息，确认执行?', true)) {
            $this->info('即将开始执行程序...');
            $version = $this->getVersion('version_num');

            $handlerFile = database_path('seeders/v' . $version . '/DataUpdateHandler.php');
            if (file_exists($handlerFile)) {
                require_once $handlerFile;
            }

            if (class_exists('DataUpdateHandler')) {
                app()->get('DataUpdateHandler');
            }
            $this->newLine();
            $this->info('执行成功, 请重启守护进程重新登录...');
        }
    }

    /**
     * 读取版本号.
     */
    protected function getVersion(string $key = ''): array|string
    {
        $version_arr    = [];
        $curent_version = @file(base_path('.version'));
        foreach ($curent_version as $val) {
            [$k, $v]         = explode('=', $val);
            $version_arr[$k] = $v;
        }
        return $key ? trim($version_arr[$key]) : $version_arr;
    }
}
