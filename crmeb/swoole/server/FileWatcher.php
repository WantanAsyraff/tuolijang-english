<?php

declare(strict_types=1);


namespace crmeb\swoole\server;

use Swoole\Timer;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

/**
 * 文件监听
 * Class FileWatcher.
 */
class FileWatcher
{
    protected $finder;

    protected $files = [];

    public function __construct($directory, $exclude, $name)
    {
        $this->finder = new Finder();
        $this->finder->files()
            ->name($name)
            ->in($directory)
            ->exclude($exclude);
    }

    public function watch(callable $callback)
    {
        $this->files = $this->findFiles();

        Timer::tick(1000, function () use ($callback) {
            $files = $this->findFiles();

            foreach ($files as $path => $time) {
                if (empty($this->files[$path]) || $this->files[$path] != $time) {
                    call_user_func($callback, [$path]);
                    break;
                }
            }

            $this->files = $files;
        });
    }

    protected function findFiles()
    {
        $files = [];
        /** @var SplFileInfo $f */
        foreach ($this->finder as $f) {
            $files[$f->getRealpath()] = $f->getMTime();
        }
        return $files;
    }
}
