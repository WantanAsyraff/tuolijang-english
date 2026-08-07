<?php

declare(strict_types=1);


namespace App\Http\Controller;

use App\Http\Service\Company\CompanyService;
use App\Http\Service\System\RolesService;
use crmeb\basic\BaseController;
use crmeb\utils\Regex;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Predis\Client;
use Predis\Response\Status;

/**
 * 程序安装控制器
 * Class InstallController.
 */
class Install extends BaseController
{
    /** 安装时可选的默认数据文件 */
    private const OPTIONAL_DEFAULT_SQL = 'defaultData.sql';

    /** public/install 目录中基础安装必须存在并执行的SQL */
    private const REQUIRED_INSTALL_SQL_FILES = [
        'config.sql',
        'dict.sql',
        'crud.sql',
        'develop.sql',
    ];

    /** public/install SQL执行顺序，未知新增SQL排在这些文件之后按文件名执行 */
    private const INSTALL_SQL_EXECUTION_ORDER = [
        'config.sql',
        'dict.sql',
        'crud.sql',
        'develop.sql',
    ];

    private string $Title = '';

    private string $Powered = 'Powered by Tuoluojiang';

    /** 初始化SQL语句数量（非static，避免Swoole常驻内存残留） */
    private int $countInitData = 0;

    /** 安装任务总数（SQL有效执行项 + 收尾任务） */
    private int $countTotal = 0;

    /** SQL执行后的安装收尾任务数 */
    private int $countFinishTasks = 5;

    public function index($step = 1)
    {
        $language = strtolower((string) request()->cookie('language', 'zh-cn'));
        App::setLocale(in_array($language, ['zh-cn', 'en'], true) ? $language : 'zh-cn');
        $this->Title = __('frontend.install.title');

        if (file_exists(public_path('install/install.lock'))) {
            return __('frontend.install.already_installed');
        }
        @set_time_limit(1000);
        if (version_compare(PHP_VERSION, '8.0.0', '<')) {
            return __('frontend.install.php_too_old');
        }
        date_default_timezone_set('PRC');
        error_reporting(E_ALL & ~E_NOTICE);
        $configFile = '.env';
        if (! file_exists(base_path($configFile))) {
            return __('frontend.install.files_missing');
        }
        $step = (int) $step;
        if ($step < 1 || $step > 5) {
            return redirect('/install/index/1');
        }
        return $this->{'step' . $step}();
    }

    /**
     * 写入安装信息.
     */
    public function installlog(): bool
    {
        $mt_rand_str  = $this->sp_random_string(6);
        $str_constant = '<?php' . PHP_EOL . "define('INSTALL_DATE'," . time() . ');' . PHP_EOL . "define('SERIALNUMBER','" . $mt_rand_str . "');";
        return @file_put_contents(base_path('.constant'), $str_constant) !== false;
    }
    /**
     * 随机字符串.
     */
    public function sp_random_string(int $len = 8): string
    {
        if ($len <= 0) {
            return '';
        }
        // 使用 cryptographically secure 随机字节生成字母数字字符串
        $bytes = random_bytes((int) ceil($len / 2));
        return substr(bin2hex($bytes), 0, $len);
    }

    /**
     * 测试目录是否可写.
     */
    public function testwrite($d)
    {
        if (is_file($d)) {
            if (is_writeable($d)) {
                return true;
            }
            return false;
        }
        $tfile = '_test.txt';
        $fp    = @fopen($d . '/' . $tfile, 'w');
        if (! $fp) {
            return false;
        }
        fclose($fp);
        $rs = @unlink($d . '/' . $tfile);
        if ($rs) {
            return true;
        }
        return false;
    }

    /**
     * 获取客户端IP地址.
     */
    public function get_client_ip(): string
    {
        try {
            return $this->request->ip() ?: '0.0.0.0';
        } catch (\Throwable) {
            return '0.0.0.0';
        }
    }

    /**
     * 创建目录.
     */
    public function dir_create(string $path, int $mode = 0777): bool
    {
        if (is_dir($path)) {
            return true;
        }
        $path    = $this->dir_path($path);
        $temp    = explode('/', $path);
        $cur_dir = '';
        $max     = count($temp) - 1;
        for ($i = 0; $i < $max; ++$i) {
            $cur_dir .= $temp[$i] . '/';
            if (@is_dir($cur_dir)) {
                continue;
            }
            @mkdir($cur_dir, 0777, true);
            @chmod($cur_dir, 0777);
        }
        return is_dir($path);
    }
    /**
     * 目录路径.
     */
    public function dir_path($path)
    {
        $path = str_replace('\\', '/', $path);
        if (substr($path, -1) != '/') {
            $path = $path . '/';
        }
        return $path;
    }
    /**
     * 分割SQL语句.
     */
    public function sql_split($sql, $tablepre)
    {
        if ($tablepre != 'eb_') {
            $sql = str_replace('eb_', $tablepre, $sql);
        }

        $sql = preg_replace('/TYPE=(InnoDB|MyISAM|MEMORY)( DEFAULT CHARSET=[^; ]+)?/', 'ENGINE=\1 DEFAULT CHARSET=utf8mb4', $sql);

        $sql          = str_replace("\r", "\n", $sql);
        $ret          = [];
        $num          = 0;
        $queriesarray = explode(";\n", trim($sql));
        unset($sql);
        foreach ($queriesarray as $query) {
            $ret[$num] = '';
            $queries   = explode("\n", trim($query));
            $queries   = array_filter($queries);
            foreach ($queries as $query) {
                $str1 = substr($query, 0, 1);
                if ($str1 != '#' && $str1 != '-') {
                    $ret[$num] .= $query;
                }
            }
            ++$num;
        }
        return $ret;
    }

    /**
     * 递归删除文件夹.
     */
    public function delFile($dir, $file_type = '')
    {
        if (is_dir($dir)) {
            $files = scandir($dir);
            // 打开目录 //列出目录中的所有文件并去掉 . 和 ..
            foreach ($files as $filename) {
                if ($filename != '.' && $filename != '..') {
                    if (! is_dir($dir . '/' . $filename)) {
                        if (empty($file_type)) {
                            unlink($dir . '/' . $filename);
                        } else {
                            if (is_array($file_type)) {
                                // 正则匹配指定文件
                                if (preg_match($file_type[0], $filename)) {
                                    unlink($dir . '/' . $filename);
                                }
                            } else {
                                // 指定包含某些字符串的文件
                                if (stristr($filename, $file_type) != false) {
                                    unlink($dir . '/' . $filename);
                                }
                            }
                        }
                    } else {
                        $this->delFile($dir . '/' . $filename);
                        rmdir($dir . '/' . $filename);
                    }
                }
            }
        } else {
            if (file_exists($dir)) {
                unlink($dir);
            }
        }
    }

    public function envData()
    {
        return [
            [
                'name'          => 'PHP 版本',
                'function_name' => 'phpv',
                'config'        => '8.0',
                'status'        => '',
                'lowest'        => '8.0',
                'types'         => 1,
            ],
            [
                'name'          => '附件上传',
                'function_name' => 'uploadSize',
                'config'        => '>=2M',
                'status'        => '',
                'lowest'        => '2M',
                'types'         => 1,
            ],
            [
                'name'          => 'session',
                'function_name' => 'session',
                'config'        => '开启',
                'status'        => '支持',
                'lowest'        => '开启',
                'types'         => 1,
            ],
            [
                'name'          => 'gd',
                'function_name' => 'gd',
                'config'        => '开启',
                'status'        => '支持',
                'lowest'        => '开启',
                'types'         => 1,
            ],
            [
                'name'          => 'pdo',
                'function_name' => 'mysql',
                'config'        => '开启',
                'status'        => '支持',
                'lowest'        => '开启',
                'types'         => 1,
            ],
            [
                'name'          => 'bcmath',
                'function_name' => 'bcmath',
                'config'        => '开启',
                'status'        => '支持',
                'lowest'        => '开启',
                'types'         => 1,
            ],
            [
                'name'          => 'curl',
                'function_name' => 'curl',
                'config'        => '开启',
                'status'        => '支持',
                'lowest'        => '开启',
                'types'         => 1,
            ],
            [
                'name'          => 'swoole',
                'function_name' => 'swoole',
                'config'        => '已安装',
                'status'        => '未安装',
                'lowest'        => '安装',
                'types'         => 1,
            ],
            [
                'name'          => 'openssl',
                'function_name' => 'openssl',
                'config'        => '开启',
                'status'        => '支持',
                'lowest'        => '开启',
                'types'         => 1,
            ],
            [
                'name'          => 'fileinfo',
                'function_name' => 'finfo_open',
                'config'        => '支持',
                'status'        => '不支持',
                'lowest'        => '支持',
                'types'         => 1,
            ],
            [
                'name'          => 'Swoole Loader',
                'function_name' => 'swoole_loader',
                'config'        => '支持',
                'status'        => '不支持',
                'lowest'        => '支持',
                'types'         => 1,
            ],
        ];
    }

    public function funcData()
    {
        return [
            [
                'name'          => 'file_put_contents',
                'function_name' => 'file_put_contents',
                'config'        => '开启',
                'types'         => 1,
            ],
            [
                'name'          => 'imagettftext',
                'function_name' => 'imagettftext',
                'config'        => '开启',
                'types'         => 1,
            ],
        ];
    }

    /**
     * 读取版本号.
     * @return array
     */
    protected function getversion()
    {
        $version_arr    = [];
        $curent_version = @file(base_path('.version'));
        if (! $curent_version) {
            return [
                'version'  => '',
                'platform' => '',
            ];
        }
        foreach ($curent_version as $val) {
            [$k, $v]         = array_pad(explode('=', $val, 2), 2, '');
            $version_arr[$k] = $v;
        }
        return $version_arr;
    }

    /**
     * 安装步骤1.
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    private function step1()
    {
        return view('install/step1', [
            'title'   => $this->Title,
            'powered' => $this->Powered,
        ]);
    }

    /**
     * 安装步骤2.
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    private function step2()
    {
        $phpv       = @phpversion();
        $tmp        = function_exists('gd_info') ? gd_info() : [];
        $passOne    = $passTwo = true;
        $configData = $this->envData();
        foreach ($configData as &$data) {
            switch ($data['function_name']) {
                case 'phpv':
                    $data['status'] = $phpv;
                    $data['config'] = '8.0';
                    $data['lowest'] = '8.0';
                    if (version_compare(PHP_VERSION, '8.0.0', '<')) {
                        $data['types'] = 0;
                        $passOne       = false;
                    }
                    break;
                case 'gd':
                    if (empty($tmp['GD Version'])) {
                        $data['status'] = 'Off';
                        $data['types']  = 0;
                        $passOne        = false;
                    } else {
                        $data['status'] = 'On' . $tmp['GD Version'];
                    }
                    break;
                case 'swoole':
                    if (extension_loaded('swoole')) {
                        $data['status'] = '已安装';
                    } else {
                        $data['status'] = '未安装';
                        $data['types']  = 0;
                        $passOne        = false;
                    }
                    break;
                case 'mysql':
                    if (extension_loaded('pdo_mysql')) {
                        $data['status'] = '已安装';
                    } else {
                        $data['status'] = '未安装';
                        $data['types']  = 0;
                        $passOne        = false;
                    }
                    break;
                case 'uploadSize':
                    if (ini_get('file_uploads')) {
                        $data['status'] = ini_get('upload_max_filesize');
                    } else {
                        $data['status'] = '禁止上传';
                        $data['types']  = 0;
                        $passOne        = false;
                    }
                    break;
                case 'session':
                    if (function_exists('session_start')) {
                        $data['status'] = '支持';
                    } else {
                        $data['status'] = '不支持';
                        $data['types']  = 0;
                        $passOne        = false;
                    }
                    break;
                case 'curl':
                    if (function_exists('curl_init')) {
                        $data['status'] = '支持';
                    } else {
                        $data['status'] = '不支持';
                        $data['types']  = 0;
                        $passOne        = false;
                    }
                    break;
                case 'bcmath':
                    if (function_exists('bcadd')) {
                        $data['status'] = '支持';
                    } else {
                        $data['status'] = '不支持';
                        $data['types']  = 0;
                        $passOne        = false;
                    }
                    break;
                case 'openssl':
                    if (function_exists('openssl_encrypt')) {
                        $data['status'] = '支持';
                    } else {
                        $data['status'] = '不支持';
                        $data['types']  = 0;
                        $passOne        = false;
                    }
                    break;
                case 'finfo_open':
                    if (function_exists('finfo_open')) {
                        $data['status'] = '支持';
                    } else {
                        $data['status'] = '不支持';
                        $data['types']  = 0;
                        $passOne        = false;
                    }
                    break;
                case 'swoole_loader':
                    if (extension_loaded('swoole_loader')) {
                        $data['status'] = '支持';
                    } else {
                        $data['status'] = '不支持';
                        $data['types']  = 0;
                        $passOne        = false;
                    }
                    break;
            }
        }
        $funcData = $this->funcData();
        foreach ($funcData as &$func) {
            switch ($func['function_name']) {
                case 'file_put_contents':
                    if (function_exists('file_put_contents')) {
                        $func['config'] = '开启';
                    } else {
                        $func['config'] = '关闭';
                        $func['types']  = 0;
                        $passOne        = false;
                    }
                    break;
                case 'imagettftext':
                    if (function_exists('imagettftext')) {
                        $func['config'] = '开启';
                    } else {
                        $func['config'] = '关闭';
                        $func['types']  = 0;
                        $passOne        = false;
                    }
                    break;
            }
        }
        $files = [
            ['path' => '.', 'readable' => true, 'writable' => true],
            ['path' => 'public', 'readable' => true, 'writable' => false],
            ['path' => 'public/install', 'readable' => true, 'writable' => true],
            ['path' => 'database/schema/mysql-schema.sql', 'readable' => true, 'writable' => false],
            ...$this->getInstallSqlCheckFiles(),
            ['path' => 'storage', 'readable' => true, 'writable' => true],
            ['path' => '.env', 'readable' => true, 'writable' => true],
            ['path' => '.version', 'readable' => true, 'writable' => false],
        ];
        foreach ($files as $file) {
            if ($file['readable'] && ! is_readable(base_path($file['path']))) {
                $passTwo = false;
            }
            if ($file['writable'] && ! is_writeable(base_path($file['path']))) {
                $passTwo = false;
            }
        }
        $configData = array_map(fn (array $row): array => $this->localizeCheckRow($row), $configData);
        $funcData   = array_map(fn (array $row): array => $this->localizeCheckRow($row), $funcData);
        return view('install/step2', [
            'Title'      => $this->Title,
            'Powered'    => $this->Powered,
            'configData' => $configData,
            'funcData'   => $funcData,
            'passOne'    => (int) $passOne,
            'passTwo'    => (int) $passTwo,
            'files'      => $files,
        ]);
    }

    private function localizeCheckRow(array $row): array
    {
        $keys = [
            'PHP 版本' => 'php_version', '附件上传' => 'attachment_upload', '开启' => 'enabled', '关闭' => 'disabled',
            '支持' => 'supported', '不支持' => 'not_supported', '已安装' => 'installed', '未安装' => 'not_installed',
            '安装' => 'install_required', '禁止上传' => 'upload_disabled',
        ];
        foreach (['name', 'config', 'status', 'lowest'] as $field) {
            if (isset($row[$field], $keys[$row[$field]])) {
                $row[$field] = __('frontend.install.' . $keys[$row[$field]]);
            }
        }
        return $row;
    }

    /**
     * 安装步骤3.
     */

    private function step3()
    {
        if ($this->request->isMethod('POST')) {
            $post = $this->request->postMore([
                ['dbHost', ''],
                ['dbPort', ''],
                ['dbUser', ''],
                ['dbPwd', ''],
                ['dbPrefix', ''],
                ['dbName', ''],
                ['cacheDriver', 'redis'],
                ['rbHost', ''],
                ['rbPort', ''],
                ['rbNum', ''],
                ['rbPwd', ''],
                ['initData', ''],
                ['account', ''],
                ['password', ''],
                ['checkPass', ''],
            ]);
            $post = $this->normalizeInstallConfig($post);

            if ($error = $this->validateInstallConfig($post)) {
                return $this->success($error);
            }

            try {
                $dsn  = "mysql:host={$post['dbHost']};port={$post['dbPort']};dbname={$post['dbName']};charset=utf8mb4";
                $conn = new \PDO($dsn, $post['dbUser'], $post['dbPwd']);
                $conn->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
                if (version_compare($conn->getAttribute(\PDO::ATTR_SERVER_VERSION), '5.7.0', '<')) {
                    return $this->success(['code' => -5, 'msg' => __('frontend.install.mysql_too_old')]);
                }
            } catch (\Exception $e) {
                Log::error('安装数据库连接失败', ['error' => $e->getMessage()]);
                $code = $e instanceof \PDOException && isset($e->errorInfo[1]) ? (int) $e->errorInfo[1] : (int) $e->getCode();
                return $this->success(['code' => $code, 'msg' => __('frontend.install.database_connection_failed', ['error' => $e->getMessage()])]);
            }
            if ($post['cacheDriver'] == 'redis') {
                try {
                    $client = new Client([
                        'host'     => $post['rbHost'],
                        'port'     => (int) $post['rbPort'],
                        'database' => (int) $post['rbNum'],
                        'password' => $post['rbPwd'],
                        'timeout'  => 3,
                    ]);
                    $response = $client->ping();
                    if (! ($response instanceof Status && $response->getPayload() === 'PONG')) {
                        return $this->success(['code' => -3, 'msg' => __('frontend.install.redis_check_config')]);
                    }
                } catch (\Exception $e) {
                    Log::error('安装 Redis 连接失败', ['error' => $e->getMessage()]);
                    return $this->success(['code' => $e->getCode(), 'msg' => __('frontend.install.redis_connection_failed', ['error' => $e->getMessage()])]);
                }
            }
            /**
             * 修改环境变量.
             */
            modify_env([
                'DB_USERNAME'     => $post['dbUser'],
                'DB_HOST'         => $post['dbHost'],
                'DB_PORT'         => $post['dbPort'],
                'DB_DATABASE'     => $post['dbName'],
                'DB_PASSWORD'     => $post['dbPwd'],
                'DB_PREFIX'       => $post['dbPrefix'],
                'CONFIG_INIT'     => $post['initData'],
                'REDIS_HOST'      => $post['rbHost'],
                'REDIS_PASSWORD'  => $post['rbPwd'],
                'REDIS_PORT'      => $post['rbPort'],
                'REDIS_DB'        => $post['rbNum'],
                'INIT_DATA'       => $post['initData'] ? 1 : 0,
                'CACHE_DRIVER'    => $post['cacheDriver'],
                'MANAGE_ACCOUNT'  => $post['account'],
                'MANAGE_PASSWORD' => password_hash($post['password'], PASSWORD_BCRYPT),
                'REDIS_QUEUE' => time(),
            ]);
            try {
                Artisan::call('config:clear');
                DB::purge('mysql');
                DB::reconnect('mysql');
            } catch (\Throwable $e) {
                Log::error('安装刷新数据库配置失败', ['error' => $e->getMessage()]);
                return $this->success(['code' => -6, 'msg' => __('frontend.install.refresh_database_failed', ['error' => $e->getMessage()])]);
            }
            @shell_exec('php ' . base_path('bin/laravels') . ' reload');
            return $this->success(['code' => 1]);
        }
        return view('/install/step3', [
            'Title'   => $this->Title,
            'Powered' => $this->Powered,
            'form'    => [
                'dbUser'      => env('DB_USERNAME', ''),
                'dbHost'      => env('DB_HOST', '127.0.0.1'),
                'dbPort'      => env('DB_PORT', '3306'),
                'dbName'      => env('DB_DATABASE', ''),
                'dbPwd'       => env('DB_PASSWORD', ''),
                'dbPrefix'    => env('DB_PREFIX', 'eb_'),
                'cacheDriver' => 'redis',
                'rbHost'      => env('REDIS_HOST', '127.0.0.1'),
                'rbPort'      => env('REDIS_PORT', '6379'),
                'rbNum'       => env('REDIS_DB', '0'),
                'rbPwd'       => env('REDIS_PASSWORD', ''),
                'initData'    => (bool) get_env('INIT_DATA'),
                'account'     => '',
                'password'    => '',
                'checkPass'   => '',
            ],
        ]);
    }

    /**
     * 数据创建.
     */
    private function step4()
    {
        if (app('request')->isMethod('POST')) {
            $n = (int) app('request')->post('n');
            if ($n >= 99999) {
                return $this->installError(__('frontend.install.progress_invalid'));
            }
            if ($n < 0) {
                return $this->success([
                    'n'    => 0,
                    'count' => 0,
                    'msg'  => '',
                    'time' => date('Y-m-d H:i:s'),
                ]);
            }

            try {
                // 每次请求都重新计算总数（避免Swoole常驻内存残留问题）
                $this->countTotal = $this->getCounts();
            } catch (\Throwable $e) {
                Log::error('安装读取SQL文件失败', ['error' => $e->getMessage()]);
                return $this->installError(__('frontend.install.read_install_sql_failed', ['error' => $e->getMessage()]));
            }

            if ($this->countTotal <= 0) {
                return $this->installError(__('frontend.install.install_sql_empty'));
            }

            if ($n === 0) {
                try {
                    $this->dropInstallTablesBeforeInit();
                } catch (\Throwable $e) {
                    Log::error('Failed to drop install tables before initialization.', ['error' => $e->getMessage()]);
                    return $this->installError(__('frontend.install.cleanup_failed', ['error' => $e->getMessage()]));
                }
            }

            $sqlCount = $this->countTotal - $this->countFinishTasks;
            if ($n < $this->countInitData) {
                if ($initStatus = $this->createInitData($n, $this->countTotal)) {
                    return $initStatus;
                }
            }
            if ($n < $sqlCount) {
                if ($seedStatus = $this->createSeedData($n, $this->countTotal)) {
                    return $seedStatus;
                }
            }
            if ($n < $this->countTotal) {
                DB::reconnect();
                return $this->runFinishTask($n, $this->countTotal);
            }

            return $this->success([
                'n'    => 99999,
                'count' => $this->countTotal,
                'msg'  => __('frontend.install.enterprise_user_created'),
                'time' => date('Y-m-d H:i:s'),
            ]);
        }
        return view('/install/step4', [
            'Title'   => $this->Title,
            'Powered' => $this->Powered,
        ]);
    }
    /**
     * 完成安装.
     */
    private function step5()
    {
        $ip             = $this->get_client_ip();
        $curent_version = $this->getversion();
        if (! $this->installlog()) {
            return __('frontend.install.install_log_failed');
        }
        if (! @touch(public_path('install/install.lock'))) {
            return __('frontend.install.install_lock_failed');
        }
        modify_env([
            'CACHE_PREFIX' => 'TL-' . substr((string) time(), -5, 5),
        ]);
        return view('/install/step5', [
            'Title'    => $this->Title,
            'Powered'  => $this->Powered,
            'ip'       => $ip,
            'version'  => trim($curent_version['version'] ?? ''),
            'platform' => trim($curent_version['platform'] ?? ''),
            'host'     => $this->request->getHost(),
            'uid'      => config('sms.stores.yunxin.template_id.ADMIN_ORDER_UID'),
        ]);
    }
    /**
     * 创建初始化数据.
     */
    private function createInitData(int $n, int $totalCount)
    {
        $sqlFormat = $this->getSchemaSql();
        $counts    = count($sqlFormat);
        $dbPrefix  = get_env('DB_PREFIX') ?: 'eb_';

        // 添加边界检查，确保n不超过数组长度
        if ($n >= $counts) {
            return false;
        }

        $sql = trim($sqlFormat[$n]);
        ++$n;

        if (str_contains($sql, 'CREATE TABLE')) {
            preg_match('/CREATE TABLE `eb_([^ ]*)`/is', $sql, $matches);
            $sql = str_replace('`eb_', '`' . $dbPrefix, $sql); // 替换表前缀
            $dbName = isset($matches[1]) ? $dbPrefix . $matches[1] : '';
            try {
                DB::unprepared($sql);
                $message = $dbName ? __('frontend.install.table_created', ['name' => $dbName]) : '';
            } catch (\Throwable $exception) {
                Log::error('安装创建数据表失败', ['n' => $n, 'table' => $dbName, 'error' => $exception->getMessage()]);
                return $this->installError($dbName
                    ? __('frontend.install.table_failed', ['name' => $dbName, 'error' => $exception->getMessage()])
                    : __('frontend.install.sql_failed', ['error' => $exception->getMessage()]), $n, $totalCount);
            }
            return $this->success([
                'n'     => $n,
                'count' => $totalCount,
                'msg'   => $message,
                'time'  => date('Y-m-d H:i:s'),
            ]);
        }

        $sql = str_replace('`eb_', '`' . $dbPrefix, $sql); // 替换表前缀
        try {
            DB::unprepared($sql);
        } catch (\Throwable $exception) {
            Log::error('安装执行初始化SQL失败', ['n' => $n, 'error' => $exception->getMessage()]);
            return $this->installError(__('frontend.install.sql_failed', ['error' => $exception->getMessage()]), $n, $totalCount);
        }
        return $this->success([
            'n'     => $n,
            'count' => $totalCount,
            'msg'   => __('frontend.install.structure_complete'),
            'time'  => date('Y-m-d H:i:s'),
        ]);
    }
    /**
     * 创建安装种子数据.
     */
    private function createSeedData(int $n, int $totalCount)
    {
        $seedSqlFiles = $this->getSeedSqlFiles();
        $i            = $n - $this->countInitData;
        if (! isset($seedSqlFiles[$i])) {
            return false;
        }

        $seedFile = $seedSqlFiles[$i];
        ++$n;

        try {
            DB::unprepared(prefix_correction($this->readSqlFile($seedFile['path']), get_env('DB_PREFIX') ?: 'eb_'));
        } catch (\Throwable $exception) {
            Log::error('安装执行种子SQL失败', ['n' => $n, 'file' => $seedFile['path'], 'error' => $exception->getMessage()]);
            return $this->installError(__('frontend.install.seed_failed', ['name' => $seedFile['name'], 'error' => $exception->getMessage()]), $n, $totalCount);
        }

        return $this->success([
            'n'     => $n,
            'count' => $totalCount,
            'msg'   => __('frontend.install.seed_complete', ['name' => $seedFile['name']]),
            'time'  => date('Y-m-d H:i:s'),
        ]);
    }

    /**
     * 创建默认数据.
     */
    private function createDefaultData(): bool
    {
        if (get_env('INIT_DATA')) {
            $file = public_path('install/' . self::OPTIONAL_DEFAULT_SQL);
            if (! is_readable($file)) {
                throw new \RuntimeException(__('frontend.install.file_unreadable', ['file' => $file]));
            }
            DB::unprepared(prefix_correction($this->readSqlFile($file), get_env('DB_PREFIX') ?: 'eb_'));
        }
        return false;
    }

    /**
     * SQL执行完成后的安装收尾任务，同样纳入进度条计数。
     */
    private function runFinishTask(int $n, int $totalCount)
    {
        $taskIndex = $n - ($totalCount - $this->countFinishTasks);

        try {
            switch ($taskIndex) {
                case 0:
                    if (! app()->get(CompanyService::class)->install()) {
                        return $this->installError(__('frontend.install.enterprise_failed'), $n, $totalCount);
                    }
                    return $this->installProgress($n + 1, $totalCount, __('frontend.install.enterprise_complete'));

                case 1:
                    $this->createDefaultData();
                    return $this->installProgress($n + 1, $totalCount, get_env('INIT_DATA') ? __('frontend.install.default_complete') : __('frontend.install.default_skipped'));

                case 2:
                    modify_env([
                        'LARAVELS_TIMER'            => true,
                        'LARAVELS_WEBSOCKET_ENABLE' => true,
                        'QUEUE_CONNECTION'          => 'redis',
                    ]);
                    return $this->installProgress($n + 1, $totalCount, __('frontend.install.environment_complete'));

                case 3:
                    app()->get(RolesService::class)->initRules();
                    Cache::forget('install:schema:parsed');
                    Cache::forget('install:config:parsed');
                    Cache::forget('install:schema:parsed:v2');
                    Cache::forget('install:config:parsed:v2');
                    Cache::forget('install:schema:parsed:v3');
                    Cache::forget('install:config:parsed:v3');
                    Cache::forget('install:schema:parsed:v4');
                    @shell_exec('php ' . base_path('bin/laravels') . ' reload');
                    return $this->installProgress($n + 1, $totalCount, __('frontend.install.roles_complete'));

                case 4:
                    $exitCode = Artisan::call('fix:customer-json-fields', [
                        '--force' => true,
                    ]);
                    if ($exitCode !== 0) {
                        throw new \RuntimeException(trim(Artisan::output()) ?: __('frontend.install.customer_json_failed'));
                    }
                    return $this->installProgress($n + 1, $totalCount, __('frontend.install.customer_json_complete'));
            }
        } catch (\Throwable $e) {
            Log::error('安装收尾任务失败', ['task' => $taskIndex, 'error' => $e->getMessage()]);
            return $this->installError(__('frontend.install.task_failed', ['task' => $this->finishTaskName($taskIndex), 'error' => $e->getMessage()]), $n, $totalCount);
        }

        return $this->installError(__('frontend.install.unknown_task'), $n, $totalCount);
    }

    /**
     * 收尾任务名称，用于错误提示。
     */
    private function finishTaskName(int $taskIndex): string
    {
        return match ($taskIndex) {
            0       => __('frontend.install.task_enterprise'),
            1       => __('frontend.install.task_default'),
            2       => __('frontend.install.task_environment'),
            3       => __('frontend.install.task_roles'),
            4       => __('frontend.install.task_customer_json'),
            default => __('frontend.install.task_execute'),
        };
    }

    /**
     * 获取初始化数据和配置数据的总数.
     */
    private function getCounts(): int
    {
        $initCount           = count($this->getSchemaSql());
        $this->countInitData = $initCount;
        return $initCount + count($this->getSeedSqlFiles()) + $this->countFinishTasks;
    }

    /**
     * 获取并缓存初始化SQL语句数组.
     */
    private function getSchemaSql(): array
    {
        return Cache::remember('install:schema:parsed:v4', 3600, function () {
            $file = database_path('schema/mysql-schema.sql');
            $sqlData = $this->readSqlFile($file);
            return $this->filterInstallSqlStatements($this->sql_split($sqlData, 'eb_'));
        });
    }

    /**
     * 获取安装流程需要整文件执行的种子SQL.
     */
    private function getSeedSqlFiles(): array
    {
        $files = array_map(fn (string $fileName): array => [
            'name' => $this->installSqlDisplayName($fileName),
            'path' => public_path('install/' . $fileName),
        ], $this->getInstallSqlFileNames());

        foreach ($files as $file) {
            if (! is_readable($file['path'])) {
                throw new \RuntimeException(__('frontend.install.file_unreadable', ['file' => $file['path']]));
            }
        }

        return $files;
    }

    /**
     * 仅保留真实需要执行的SQL，避免空语句、注释标记影响进度百分比。
     */
    private function filterInstallSqlStatements(array $sqlStatements): array
    {
        return array_values(array_filter(array_map(static fn ($sql) => trim((string) $sql), $sqlStatements), function (string $sql): bool {
            return $sql !== '' && ! $this->isMysqlDumpSessionStatement($sql);
        }));
    }

    /**
     * 跳过 mysqldump 导出的会话环境 SET 语句，避免分批安装时会话变量丢失。
     */
    private function isMysqlDumpSessionStatement(string $sql): bool
    {
        $sql = trim($sql);
        if ($sql === '') {
            return true;
        }

        $sql = preg_replace('/\/\*M!\d+.*?\*\//is', '', $sql) ?? $sql;
        $sql = preg_replace('/\/\*!\d+\s+SET\s+.*?\*\//is', '', $sql) ?? $sql;

        return trim($sql) === '';
    }

    /**
     * 读取安装SQL文件，确保失败时能中断安装并返回明确原因。
     */
    private function readSqlFile(string $file): string
    {
        if (! is_readable($file)) {
            throw new \RuntimeException(__('frontend.install.file_unreadable', ['file' => $file]));
        }

        $content = file_get_contents($file);
        if ($content === false) {
            throw new \RuntimeException(__('frontend.install.read_install_sql_failed', ['error' => $file]));
        }

        return $content;
    }

    /**
     * 安装检测所需SQL文件，defaultData.sql 是可选默认数据，不阻断基础安装检测。
     */
    private function dropInstallTablesBeforeInit(): void
    {
        $sqlContents = [$this->readSqlFile(database_path('schema/mysql-schema.sql'))];
        foreach ($this->getInstallSqlFileNames() as $fileName) {
            $path = public_path('install/' . $fileName);
            if (is_readable($path)) {
                $sqlContents[] = $this->readSqlFile($path);
            }
        }

        $matches = [];
        preg_match_all('/CREATE\s+TABLE\s+`eb_([A-Za-z0-9_]+)`/i', implode("\n", $sqlContents), $matches);
        $baseTables = array_values(array_unique($matches[1] ?? []));
        if (! $baseTables) {
            return;
        }

        $dbPrefix = get_env('DB_PREFIX') ?: 'eb_';
        $prefixes = array_values(array_unique(['eb_', $dbPrefix]));
        $tables = [];
        foreach ($prefixes as $prefix) {
            foreach ($baseTables as $table) {
                $tables[] = $prefix . $table;
            }
        }

        $placeholders = implode(',', array_fill(0, count($tables), '?'));
        $existing = array_map(static function ($row): string {
            return $row->table_name;
        }, DB::select(
            'SELECT table_name FROM information_schema.tables WHERE table_schema = ? AND table_name IN (' . $placeholders . ')',
            array_merge([DB::connection()->getDatabaseName()], $tables)
        ));

        if (! $existing) {
            return;
        }

        $quotedTables = array_map(static function (string $table): string {
            return '`' . str_replace('`', '``', $table) . '`';
        }, $existing);

        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        try {
            DB::statement('DROP TABLE IF EXISTS ' . implode(',', $quotedTables));
        } finally {
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
        }
    }

    private function getInstallSqlCheckFiles(): array
    {
        return array_map(static fn (string $fileName): array => [
            'path'     => 'public/install/' . $fileName,
            'readable' => true,
            'writable' => false,
        ], $this->getInstallSqlFileNames());
    }

    /**
     * 获取 public/install 目录下需要安装执行的SQL文件名。
     */
    private function getInstallSqlFileNames(): array
    {
        $installPath = public_path('install');
        $discovered  = [];
        foreach (glob($installPath . '/*.sql') ?: [] as $path) {
            $discovered[] = basename($path);
        }

        $required = self::REQUIRED_INSTALL_SQL_FILES;
        $files    = array_values(array_unique(array_merge($required, $discovered)));
        $files    = array_values(array_filter($files, static function (string $fileName): bool {
            if ($fileName === self::OPTIONAL_DEFAULT_SQL) {
                return false;
            }
            return str_ends_with($fileName, '.sql');
        }));

        usort($files, static function (string $left, string $right): int {
            $leftIndex  = array_search($left, self::INSTALL_SQL_EXECUTION_ORDER, true);
            $rightIndex = array_search($right, self::INSTALL_SQL_EXECUTION_ORDER, true);
            $leftIndex  = $leftIndex === false ? PHP_INT_MAX : $leftIndex;
            $rightIndex = $rightIndex === false ? PHP_INT_MAX : $rightIndex;

            return $leftIndex === $rightIndex ? strcmp($left, $right) : $leftIndex <=> $rightIndex;
        });

        return $files;
    }

    /**
     * 安装进度中展示SQL文件含义。
     */
    private function installSqlDisplayName(string $fileName): string
    {
        return match ($fileName) {
            'config.sql'  => __('frontend.install.sql_config'),
            'dict.sql'    => __('frontend.install.sql_dict'),
            'crud.sql'    => __('frontend.install.sql_crud'),
            'develop.sql' => __('frontend.install.sql_develop'),
            default       => pathinfo($fileName, PATHINFO_FILENAME),
        };
    }

    /**
     * 规整安装配置，避免空格、端口类型和布尔值导致误判。
     */
    private function normalizeInstallConfig(array $post): array
    {
        foreach (['dbHost', 'dbPort', 'dbUser', 'dbName', 'dbPrefix', 'cacheDriver', 'rbHost', 'rbPort', 'rbNum', 'account'] as $key) {
            $post[$key] = trim((string) ($post[$key] ?? ''));
        }
        $post['account'] = preg_replace('/[\s()\-]/', '', $post['account']);
        $post['dbPrefix']    = $post['dbPrefix'] ?: 'eb_';
        $post['cacheDriver'] = $post['cacheDriver'] ?: 'redis';
        $post['rbNum']       = $post['rbNum'] === '' ? '0' : $post['rbNum'];
        $post['initData']    = filter_var($post['initData'] ?? false, FILTER_VALIDATE_BOOLEAN);
        $post['dbPwd']       = (string) ($post['dbPwd'] ?? '');
        $post['rbPwd']       = (string) ($post['rbPwd'] ?? '');
        $post['password']    = (string) ($post['password'] ?? '');
        $post['checkPass']   = (string) ($post['checkPass'] ?? '');

        return $post;
    }

    /**
     * 安装表单后端兜底校验，防止绕过前端导致错误配置写入 .env。
     */
    private function validateInstallConfig(array $post): ?array
    {
        foreach (['dbHost' => 'database_host', 'dbPort' => 'database_port', 'dbUser' => 'database_user', 'dbName' => 'database_name'] as $key => $labelKey) {
            if ($post[$key] === '') {
                $field = rtrim(__('frontend.install.' . $labelKey), ':：');
                return ['code' => -10, 'field' => $key, 'msg' => __('frontend.install.required_field', ['field' => $field])];
            }
        }
        if (! ctype_digit($post['dbPort']) || (int) $post['dbPort'] < 1 || (int) $post['dbPort'] > 65535) {
            return ['code' => -10, 'field' => 'dbPort', 'msg' => __('frontend.install.invalid_database_port')];
        }
        if (! preg_match('/^[A-Za-z0-9_]+$/', $post['dbName'])) {
            return ['code' => -10, 'field' => 'dbName', 'msg' => __('frontend.install.invalid_database_name')];
        }
        if (! preg_match('/^[A-Za-z][A-Za-z0-9_]*_$/', $post['dbPrefix'])) {
            return ['code' => -10, 'field' => 'dbPrefix', 'msg' => __('frontend.install.invalid_database_prefix')];
        }
        if (! in_array($post['cacheDriver'], ['redis'], true)) {
            return ['code' => -10, 'field' => 'cacheDriver', 'msg' => __('frontend.install.redis_only')];
        }
        $adminAccount = preg_replace('/[\s()\-]/', '', $post['account']);
        if (! preg_match('/^\+?[1-9]\d{6,14}$/', $adminAccount)) {
            return ['code' => -2, 'field' => 'account', 'msg' => __('frontend.install.invalid_admin_mobile')];
        }
        $post['account'] = $adminAccount;
        if (strlen($post['password']) < 6) {
            return ['code' => -10, 'field' => 'password', 'msg' => __('frontend.install.admin_password_min')];
        }
        if (! hash_equals($post['password'], $post['checkPass'])) {
            return ['code' => -10, 'field' => 'checkPass', 'msg' => __('frontend.install.admin_password_mismatch')];
        }
        if ($post['rbHost'] === '') {
            return ['code' => -10, 'field' => 'rbHost', 'msg' => __('frontend.install.redis_host_required')];
        }
        if (! ctype_digit($post['rbPort']) || (int) $post['rbPort'] < 1 || (int) $post['rbPort'] > 65535) {
            return ['code' => -10, 'field' => 'rbPort', 'msg' => __('frontend.install.invalid_redis_port_backend')];
        }
        if (! ctype_digit($post['rbNum']) || (int) $post['rbNum'] < 0 || (int) $post['rbNum'] > 15) {
            return ['code' => -10, 'field' => 'rbNum', 'msg' => __('frontend.install.invalid_redis_database_backend')];
        }
        if (! is_readable(base_path('.env')) || ! is_writable(base_path('.env'))) {
            return ['code' => -10, 'field' => 'dbName', 'msg' => __('frontend.install.env_permissions')];
        }

        return null;
    }

    /**
     * 安装进度统一错误响应，前端收到 error=1 后停止轮询。
     */
    private function installError(string $message, int $n = 0, int $count = 0)
    {
        return $this->success([
            'error' => 1,
            'n'     => $n,
            'count' => $count ?: max($this->countTotal, 1),
            'msg'   => $message,
            'time'  => date('Y-m-d H:i:s'),
        ]);
    }

    /**
     * 安装进度统一响应。
     */
    private function installProgress(int $n, int $count, string $message)
    {
        return $this->success([
            'n'     => $n,
            'count' => $count,
            'msg'   => $message,
            'time'  => date('Y-m-d H:i:s'),
        ]);
    }
}
