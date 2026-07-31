<?php

declare(strict_types=1);

use App\Http\Controller\Install;
use Illuminate\Support\Facades\Route;

// 安装程序路由
Route::redirect('install', 'install/index/1');
Route::match(['get', 'post'], 'install/index/{step}', [Install::class, 'index'])->whereNumber('step');
