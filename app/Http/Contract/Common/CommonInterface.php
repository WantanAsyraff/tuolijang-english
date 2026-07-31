<?php

declare(strict_types=1);


namespace App\Http\Contract\Common;

interface CommonInterface
{
    /**
     * 图像验证码
     * @return mixed
     */
    public function captcha(): array;

    /**
     * 获得短信发送key.
     */
    public function smsVerifyKey(): array;

    /**
     * 短信验证码
     * @param mixed $phone
     * @param mixed $key
     * @param mixed $types
     */
    public function smsVerifyCode($phone, $key, $types): bool;

    /**
     * 文件上传.
     * @return mixed
     */
    public function uploadFromFile(string $file, array $option): array;

    /**
     * 文件内容上传.
     * @return mixed
     */
    public function uploadFromResource(string $file, array $option): array;

    /**
     * 通过链接上传.
     * @return mixed
     */
    public function uploadFromUrl(string $url, array $option): array;

    /**
     * 城市数据树形结构.
     */
    public function getCityTree(): array;
}
