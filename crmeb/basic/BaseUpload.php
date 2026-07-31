<?php

declare(strict_types=1);


namespace crmeb\basic;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Config;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

/**
 * Class BaseUpload.
 */
abstract class BaseUpload extends BaseStorage
{
    /**
     * 缩略图.
     * @var string[]
     */
    protected $thumb = ['big', 'mid', 'small'];

    /**
     * 缩略图配置.
     * @var array
     */
    protected $thumbConfig = [
        'thumb_big_height'   => 800,
        'thumb_big_width'    => 800,
        'thumb_mid_height'   => 300,
        'thumb_mid_width'    => 300,
        'thumb_small_height' => 100,
        'thumb_small_width'  => 100,
    ];

    /**
     * 水印配置.
     * @var array
     */
    protected $waterConfig = [
        'image_watermark_status' => 0,
        'watermark_type'         => 1,
        'watermark_image'        => '',
        'watermark_opacity'      => 0,
        'watermark_position'     => 1,
        'watermark_rotate'       => 0,
        'watermark_text'         => '',
        'watermark_text_angle'   => '',
        'watermark_text_color'   => '#000000',
        'watermark_text_size'    => '5',
        'watermark_text_font'    => '',
        'watermark_x'            => 0,
        'watermark_y'            => 0,
    ];

    /**
     * 图片信息.
     * @var array
     */
    protected $fileInfo;

    /**
     * 下载图片信息.
     */
    protected $downFileInfo;

    /**
     * 要生成缩略图、水印的图片地址
     * @var string
     */
    protected $filePath;

    /**
     * 验证配置.
     * @var string
     */
    protected $validate;

    /**
     * 验证配置.
     * @var array
     */
    protected $validateArr;

    /**
     * 保存路径.
     * @var string
     */
    protected $path;

    /**
     * 设置处理缩略图、水印图片路径.
     * @return $this
     */
    public function setFilepath(string $filePath)
    {
        $this->filePath = substr($filePath, 0, 1) === '.' ? substr($filePath, 1) : $filePath;
        return $this;
    }

    /**
     * 上传文件路径.
     * @return $this
     */
    public function to(string $path)
    {
        $this->path = $path;
        return $this;
    }

    /**
     * 获取文件信息.
     * @return array
     */
    public function getFileInfo()
    {
        return $this->fileInfo;
    }

    /**
     * 设置验证规则.
     * @return $this
     */
    public function validate(?array $validate = null)
    {
        if (is_null($validate)) {
            $validate = $this->getConfig();
        }
        $this->validateArr = $validate;
        $this->extractValidate($validate);
        return $this;
    }

    /**
     * 上传验证
     */
    public function validator(UploadedFile $file)
    {
        if ($file->getSize() > $this->validateArr['max']) {
            throw new FileException(__('common.upload.filesizeRrror'));
        }
        if (! in_array($file->extension(), $this->validateArr['mimes'])) {
            throw new FileException(__('common.upload.fileExtError'));
        }
        if (! in_array($file->getClientMimeType(), $this->validateArr['mimetypes'])) {
            throw new FileException(__('common.upload.fileMineError'));
        }
    }

    /**
     * 获取上传信息.
     * @return array
     */
    public function getUploadInfo()
    {
        if (isset($this->fileInfo->filePath)) {
            if (! str_contains($this->fileInfo->filePath, 'http')) {
                $url = request()->getHost() . $this->fileInfo->filePath;
            } else {
                $url = $this->fileInfo->filePath;
            }
            if (! isset($this->fileInfo->fileSize) && ! isset($this->fileInfo->fileType)) {
                $headers = $this->getFileHeaders($url);
            } else {
                $headers['size'] = 0;
                $headers['type'] = null;
            }
            return [
                'name'      => $this->fileInfo->fileName,
                'real_name' => $this->fileInfo->realName ?? '',
                'size'      => $this->fileInfo->fileSize ?? $headers['size'],
                'type'      => $this->fileInfo->fileType ?? $headers['type'],
                'dir'       => $this->fileInfo->filePath,
                'ext'       => $this->fileInfo->fileExt ?? '',
                'time'      => time(),
            ];
        }
        return [];
    }

    /**
     * 文件上传.
     * @return mixed
     */
    abstract public function move(string $file = 'file');

    /**
     * 文件流上传.
     * @param mixed $fileContent
     * @return mixed
     */
    abstract public function stream($fileContent, ?string $key = null);

    /**
     * 删除文件.
     * @return mixed
     */
    abstract public function delete(string $filePath);

    /**
     * 获取上传密钥.
     * @return mixed
     */
    abstract public function getTempKeys();

    protected function initialize(array $config)
    {
        $this->fileInfo = new \stdClass();
    }

    /**
     * 验证目录是否正确.
     * @return false|string
     */
    protected function getUploadPath(string $key)
    {
        $path = ($this->path ? $this->path . '/' : '') . $key;
        if ($path && $path[0] === '/') {
            $path = substr($path, 1);
        }
        return $path;
    }

    /**
     * 验证合法上传域名.
     */
    protected function checkUploadUrl(string $url): string
    {
        if ($url && ! str_contains($url, 'http')) {
            $url = 'http://' . $url;
        }
        return rtrim($url, '/');
    }

    /**
     * 获取系统配置.
     * @return mixed
     */
    protected function getConfig()
    {
        $config = Config::get($this->configFile . '.stores.' . $this->name, []);
        if (empty($config)) {
            // 优先读取系统设置中的文件类型配置
            $uploadMime = sys_config('upload_mime');

            if (! empty($uploadMime)) {
                // 确保是数组格式
                $mimeTypes = is_array($uploadMime) ? $uploadMime : (is_string($uploadMime) ? explode(',', $uploadMime) : []);
                $mimeTypes = array_map('trim', $mimeTypes);

                // 根据 MIME 类型反向查找扩展名
                $extensions = [];
                $mimeToExt  = Config::get($this->configFile . '.mime_types', []);
                $reverseMap = array_flip($mimeToExt); // MIME => 扩展名

                foreach ($mimeTypes as $mime) {
                    // 直接匹配
                    if (isset($reverseMap[$mime])) {
                        $extensions[] = $reverseMap[$mime];
                    } else {
                        // 模糊匹配（处理多个扩展名对应同一 MIME 类型的情况）
                        foreach ($mimeToExt as $ext => $mappedMime) {
                            if ($mappedMime === $mime) {
                                $extensions[] = $ext;
                            }
                        }
                    }
                }

                $config['mimes']     = array_unique($extensions);
                $config['mimetypes'] = $mimeTypes;
            } else {
                // 使用配置文件默认值
                $config['mimes']     = Config::get($this->configFile . '.fileExt', []);
                $config['mimetypes'] = Config::get($this->configFile . '.fileMime', []);
            }

            $config['max'] = Config::get($this->configFile . '.filesize', []);
        }
        return $config;
    }

    /**
     * 提取上传验证
     */
    protected function extractValidate(array $validateArray)
    {
        $validate = [];
        foreach ($validateArray as $key => $value) {
            $validate[] = $key . ':' . (is_array($value) ? implode(',', $value) : $value);
        }
        $this->validate = implode('|', $validate);
        unset($validate);
    }

    /**
     * 提取文件名.
     * @param mixed $withExt
     * @return string
     */
    protected function saveFileName(?string $path = null, string $ext = 'jpg', $withExt = true)
    {
        $name = ($path ? substr(md5($path), 0, 5) : '') . date('YmdHis') . rand(0, 9999);
        return $withExt ? $name . '.' . strtolower($ext) : $name;
    }

    /**
     * 获取文件类型和大小.
     * @param bool $isData
     * @return array
     */
    protected function getFileHeaders(string $url, $isData = true)
    {
        stream_context_set_default(['ssl' => ['verify_peer' => false, 'verify_peer_name' => false]]);
        $header['size'] = 0;
        $header['type'] = null;
        if (! $isData) {
            return $header;
        }
        try {
            $headerArray = get_headers(str_replace('\\', '/', $url), true);
            if (! isset($headerArray['Content-Length'])) {
                $header['size'] = 0;
            }
            if (! isset($headerArray['Content-Type'])) {
                $header['type'] = 'image/jpeg';
            }
            if (is_array($headerArray['Content-Length']) && count($headerArray['Content-Length']) == 2) {
                $header['size'] = $headerArray['Content-Length'][1];
            }
            if (is_array($headerArray['Content-Type']) && count($headerArray['Content-Type']) == 2) {
                $header['type'] = $headerArray['Content-Type'][1];
            }
        } catch (\Exception $e) {
        }
        return $header;
    }

    /**
     * 获取图片地址
     * @return string
     */
    protected function getFilePath(string $filePath = '', bool $is_parse_url = false)
    {
        $path = $filePath ?: $this->filePath;
        if ($is_parse_url) {
            $data = parse_url($path);
            // 远程地址处理
            if (isset($data['host'], $data['path'])) {
                if (file_exists(public_path('public' . $data['path']))) {
                    $path = $data['path'];
                }
            }
        }
        return $path;
    }

    /**
     * 实例化上传.
     * @return mixed
     */
    abstract protected function app();
}
