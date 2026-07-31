<?php

declare(strict_types=1);


namespace App\Console\Commands;

use Illuminate\Console\Command;

class InitCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'crmeb:init';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '';

    /**
     * Create a new command instance.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        if (! file_exists(base_path('.env'))) {
            @copy(base_path('.env.example'), base_path('.env'));
        }
        $this->call('jwt:secret');
        $this->call('key:generate');
        $this->info('Init key success!');
        $this->call('serve', ['--port' => '1215']);
        $this->line('安装服务已启动...');
    }
}
