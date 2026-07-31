<?php

declare(strict_types=1);


namespace crmeb\utils;

use Illuminate\Database\Migrations\Migration as LaravelMigration;

/**
 * Class Migration.
 */
class Migration extends LaravelMigration
{
    protected $prefix;

    /**
     * Migration constructor.
     */
    public function __construct()
    {
        $this->prefix = '';
    }

    /**
     * 获取表明.
     * @return string
     */
    protected function table(string $name)
    {
        return $this->prefix . $name;
    }
}
