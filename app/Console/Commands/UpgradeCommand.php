<?php

declare(strict_types=1);


namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Database\Migrations\Migrator;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Symfony\Component\Process\Process;

class UpgradeCommand extends Command
{
    /**
     * @var string
     */
    protected $signature = 'tl:up
                            {--target= : 指定升级资源版本号，如 2.4}
                            {--skip-composer : 跳过 composer install}
                            {--pretend : 模拟执行升级，仅检查并输出将执行的迁移/SQL，不修改数据}
                            {--force : 跳过备份确认，直接执行升级}';

    /**
     * Create a new command instance.
     */
    public function __construct()
    {
        parent::__construct();
    }

    public function handle(): void
    {
        if ($this->isPretend() || $this->option('force') || $this->confirm('为了您的数据安全, 更新前请确认是否做好数据备份? (数据库/项目文件)', true)) {
            $this->upgrade();
        }
    }

    /**
     * 读取版本号.
     */
    protected function getVersion(string $key = ''): array|string
    {
        $versionArr     = [];
        $currentVersion = @file(base_path('.version'));
        if ($currentVersion === false) {
            return $key ? '' : [];
        }
        foreach ($currentVersion as $val) {
            if (! str_contains($val, '=')) {
                continue;
            }
            [$k, $v] = explode('=', $val, 2);
            $versionArr[trim($k)] = trim($v);
        }
        return $key ? ($versionArr[$key] ?? '') : $versionArr;
    }

    private function upgrade(): void
    {
        $version = $this->resolveUpgradeVersion();
        if ($version === '') {
            $this->error('未能识别升级版本号，请检查 .version 或使用 --target=2.4 指定');
            return;
        }

        $this->info('即将开始执行升级程序, 目标版本: v' . $version);
        $this->line('版本来源: ' . $this->describeVersionSource());
        if ($this->isPretend()) {
            $this->warn('当前为模拟执行模式，不会安装依赖、执行数据更新、写入 SQL 或清理缓存。');
        }

        if ($this->isPretend()) {
            $this->warn('模拟执行：已跳过 Composer 安装');
        } elseif (! $this->option('skip-composer')) {
            $this->installComposerPackages();
        } else {
            $this->warn('已跳过 Composer 安装');
        }
        $this->newLine();

        // update database migration files
        $this->info('正在检查是否需要更新数据库...');
        $migrationSuccess = $this->runMigrations($version);

        $this->info('检查数据结构升级...');
        $handlerSuccess = $this->runDataUpdateHandler($version);

        $sqlSuccess = $this->runSqlSeeder($version);

        $commandSuccess = $this->runVersionCommands($version);

        if (! $migrationSuccess || ! $handlerSuccess || ! $sqlSuccess || ! $commandSuccess) {
            $this->warn('升级过程中存在失败项，请根据上方提示和日志处理后重新执行。');
            return;
        }

        if (! $this->isPretend()) {
            $this->info('清理缓存中...');
            Artisan::call('cache:clear');
            Artisan::call('config:clear');
            $this->info('缓存清理完成.');
        }

        $this->newLine();
        if ($this->isPretend()) {
            $this->info('模拟执行完成，请根据输出确认无误后去掉 --pretend 正式执行升级。');
        } else {
            $this->info('更新完成, 您的系统版本已成功升级为' . $version . ',建议您重启服务...');
        }
    }

    private function resolveUpgradeVersion(): string
    {
        $optionVersion = trim((string) $this->option('target'));
        if ($optionVersion !== '') {
            return ltrim($optionVersion, 'vV');
        }

        $versionName = (string) $this->getVersion('version');
        if (preg_match('/v(\d+(?:\.\d+)+)$/i', $versionName, $matches)) {
            return $matches[1];
        }

        return (string) $this->getVersion('version_num');
    }

    private function describeVersionSource(): string
    {
        if (trim((string) $this->option('target')) !== '') {
            return '--target';
        }
        if (preg_match('/v(\d+(?:\.\d+)+)$/i', (string) $this->getVersion('version'))) {
            return '.version.version';
        }

        return '.version.version_num';
    }

    private function installComposerPackages(): void
    {
        $this->info('开始执行 Composer 安装...');
        $process = Process::fromShellCommandline('composer install', base_path(), null, null, null);
        $process->setTimeout(null);
        $process->run(function (string $type, string $buffer): void {
            $this->output->write($buffer);
        });

        if (! $process->isSuccessful()) {
            throw new \RuntimeException('Composer 安装失败，请检查上方输出');
        }

        $this->info('Composer 安装成功!');
    }

    private function isPretend(): bool
    {
        return (bool) $this->option('pretend');
    }

    private function runMigrations(string $version): bool
    {
        $migrationDir = database_path('migrations/v' . $version);
        if (! is_dir($migrationDir)) {
            $this->info('数据库无需更新');
            return true;
        }

        $files = glob($migrationDir . '/*.php') ?: [];
        sort($files, SORT_NATURAL);
        if (! $files) {
            $this->info('数据库无需更新');
            return true;
        }

        $isAllMigrationSucc = true;
        $this->newLine();
        $this->info('开始执行数据迁移...');
        $this->newLine();
        foreach ($files as $file) {
            $path = 'database/migrations/v' . $version . '/' . basename($file);
            $this->info('执行文件: ' . $path);
            try {
                $this->runMigrationFile($file);
            } catch (\Throwable $e) {
                $isAllMigrationSucc = false;
                $this->warn('数据库更新失败: ' . $e->getMessage());
                $this->warn('迁移文件地址: ' . $path);
                $this->newLine();
                Log::error($e->getMessage(), ['file path' => $path]);
            }
        }

        $this->newLine();
        if ($isAllMigrationSucc) {
            $this->info('数据库更新成功!');
        } else {
            $this->warn('部分数据更新失败, 详情请查看日志');
        }

        return $isAllMigrationSucc;
    }

    private function runMigrationFile(string $file): void
    {
        if ($this->isPretend()) {
            $this->pretendMigrationFile($file);
            return;
        }

        /** @var Migrator $migrator */
        $migrator   = app('migrator');
        $repository = app('migration.repository');

        if (! $repository->repositoryExists()) {
            $repository->createRepository();
            $this->info('已创建 migrations 迁移记录表');
        }

        $migrator->setOutput($this->output)->run([$file], [
            'pretend' => false,
            'step'    => true,
        ]);
    }

    private function pretendMigrationFile(string $file): void
    {
        $migration = $this->resolveMigrationFile($file);
        if (! method_exists($migration, 'up')) {
            $this->warn('模拟执行：迁移文件不存在 up 方法，已跳过');
            return;
        }

        $queries = DB::connection()->pretend(function () use ($migration): void {
            $migration->up();
        });

        if (! $queries) {
            $this->line('模拟执行：该迁移未生成 SQL');
            return;
        }

        foreach ($queries as $query) {
            $this->line('  ⇂ ' . ($query['query'] ?? ''));
        }
    }

    private function resolveMigrationFile(string $file): object
    {
        $migration = require $file;
        if (is_object($migration)) {
            return $migration;
        }

        $class = Str::studly(implode('_', array_slice(explode('_', basename($file, '.php')), 4)));
        if (! class_exists($class)) {
            throw new \RuntimeException('未找到迁移类: ' . $class);
        }

        return new $class();
    }

    private function runDataUpdateHandler(string $version): bool
    {
        $handlerFile = database_path('seeders/v' . $version . '/DataUpdateHandler.php');
        if (! file_exists($handlerFile)) {
            $this->line('未发现数据结构升级处理器: ' . $handlerFile);
            return true;
        }

        if ($this->isPretend()) {
            $this->warn('模拟执行：发现数据结构升级处理器，将在正式执行时运行: ' . $handlerFile);
            return true;
        }

        try {
            require_once $handlerFile;
            if (class_exists('DataUpdateHandler')) {
                new \DataUpdateHandler();
            }
            $this->info('数据结构升级处理完成');
            return true;
        } catch (\Throwable $e) {
            $this->warn('数据结构升级失败: ' . $e->getMessage());
            Log::error($e->getMessage(), ['file path' => $handlerFile]);
            return false;
        }
    }

    private function runSqlSeeder(string $version): bool
    {
        $sqlPath = database_path('seeders/v' . $version . '.sql');
        if (! file_exists($sqlPath)) {
            $this->line('未发现 SQL 数据升级文件: ' . $sqlPath);
            return true;
        }

        try {
            $sql = prefix_correction(file_get_contents($sqlPath));
            if ($this->isPretend()) {
                $queries = DB::connection()->pretend(function () use ($sql): void {
                    DB::unprepared($sql);
                });
                $this->warn('模拟执行 SQL 数据升级文件: ' . $sqlPath);
                foreach ($queries as $query) {
                    $this->line($query['query'] ?? '');
                }
                return true;
            }

            DB::unprepared($sql);
            $this->info('SQL 数据升级完成');
            return true;
        } catch (\Throwable $e) {
            $this->warn('SQL 数据升级失败: ' . $e->getMessage());
            Log::error($e->getMessage(), ['file path' => $sqlPath]);
            return false;
        }
    }

    private function runVersionCommands(string $version): bool
    {
        $commands = $this->versionCommands($version);
        if (! $commands) {
            return true;
        }

        $this->info('执行版本数据处理命令...');
        $success = true;
        foreach ($commands as $command => $params) {
            if ($this->isPretend()) {
                $params['--dry-run'] = true;
            }

            $this->line('执行命令: php artisan ' . $command . $this->formatCommandParams($params));
            try {
                $exitCode = Artisan::call($command, $params);
                $output = trim(Artisan::output());
                if ($output !== '') {
                    $this->line($output);
                }
                if ($exitCode !== self::SUCCESS) {
                    $success = false;
                    $this->warn('命令执行失败: ' . $command . '，退出码: ' . $exitCode);
                }
            } catch (\Throwable $e) {
                $success = false;
                $this->warn('命令执行异常: ' . $command . '，错误: ' . $e->getMessage());
                Log::error($e->getMessage(), ['command' => $command, 'params' => $params]);
            }
        }

        return $success;
    }

    private function versionCommands(string $version): array
    {
        return match ($version) {
            '2.4' => [
                'customer:generate-system-numbers' => [],
                'fix:admin-roles'                  => [],
                'fix:admin-letter'                 => [],
                'fix:customer-json-fields'         => ['--force' => true],
            ],
            default => [],
        };
    }

    private function formatCommandParams(array $params): string
    {
        if (! $params) {
            return '';
        }

        $segments = [];
        foreach ($params as $key => $value) {
            if ($value === true) {
                $segments[] = $key;
                continue;
            }
            if ($value === false || $value === null) {
                continue;
            }
            $segments[] = $key . '=' . $value;
        }

        return $segments ? ' ' . implode(' ', $segments) : '';
    }
}
