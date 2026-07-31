<?php

declare(strict_types=1);


namespace App\Http\Service\WorkExternalContact;

use crmeb\basic\BaseService;
use Illuminate\Support\Facades\Cache;

/**
 * URL元数据获取服务
 */
class UrlMetadataService extends BaseService
{
    private const CACHE_PREFIX = 'url_metadata:';
    private const CACHE_TTL = 1800; // 30分钟

    /**
     * 根据URL获取元数据
     * @param string $url 目标URL
     * @return array{title: string, description: string, cover_image: string}
     */
    public function getMetadata(string $url): array
    {
        // 验证URL格式
        if (! filter_var($url, FILTER_VALIDATE_URL)) {
            throw $this->exception('无效的URL格式');
        }
        Cache::forget(self::CACHE_PREFIX . md5($url));
        $cacheKey = self::CACHE_PREFIX . md5($url);

        // 尝试从缓存获取
        $cached = Cache::get($cacheKey);
        if ($cached !== null) {
            return $cached;
        }

        // 抓取页面内容
        $html = $this->fetchUrl($url);
        if ($html === '') {
            throw $this->exception('无法获取网页内容');
        }

        // 解析元数据
        $metadata = $this->parseMetadata($html, $url);

        // 缓存结果
        Cache::put($cacheKey, $metadata, self::CACHE_TTL);

        return $metadata;
    }

    /**
     * 抓取URL内容
     */
    private function fetchUrl(string $url): string
    {
        // SSRF防护：检查是否为私有网络地址
        if ($this->isPrivateUrl($url)) {
            return '';
        }

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false); // 禁用自动重定向，手动处理
        curl_setopt($ch, CURLOPT_MAXREDIRS, 0);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)');
        curl_setopt($ch, CURLOPT_ENCODING, '');
        curl_setopt($ch, CURLOPT_HEADER, true); // 包含响应头以便解析重定向

        $maxRedirects = 5;
        $redirectCount = 0;

        do {
            $content = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

            if ($content === false) {
                curl_close($ch);
                return '';
            }

            // 检查重定向状态码
            if (in_array($httpCode, [301, 302, 303, 307, 308])) {
                // 从响应头中提取Location
                if (preg_match('/Location:\s*(\S+)/i', $content, $matches)) {
                    $redirectUrl = trim($matches[1]);

                    // 处理相对路径重定向
                    if (! str_starts_with($redirectUrl, 'http')) {
                        $parsed = parse_url($url);
                        $scheme = $parsed['scheme'] ?? 'https';
                        $host = $parsed['host'] ?? '';
                        $redirectUrl = $scheme . '://' . $host . $redirectUrl;
                    }

                    // SSRF防护：检查重定向目标是否为私有网络地址
                    if ($this->isPrivateUrl($redirectUrl)) {
                        curl_close($ch);
                        return '';
                    }

                    // 关闭当前连接，跟随重定向
                    curl_close($ch);
                    $url = $redirectUrl;
                    $ch = curl_init($url);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
                    curl_setopt($ch, CURLOPT_MAXREDIRS, 0);
                    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
                    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)');
                    curl_setopt($ch, CURLOPT_ENCODING, '');
                    curl_setopt($ch, CURLOPT_HEADER, true);

                    $redirectCount++;
                    if ($redirectCount >= $maxRedirects) {
                        curl_close($ch);
                        return '';
                    }
                    continue;
                }
            }

            break;
        } while ($redirectCount < $maxRedirects);

        // 获取实际内容（去掉响应头）
        $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $body = substr($content, $headerSize);
        curl_close($ch);

        if ($httpCode !== 200) {
            return '';
        }

        return $body;
    }

    /**
     * 检查URL是否为私有网络地址（SSRF防护）
     */
    private function isPrivateUrl(string $url): bool
    {
        $parsed = parse_url($url);
        $host = $parsed['host'] ?? '';

        // 检查是否为 localhost 或 IP 地址
        if (in_array($host, ['localhost', '127.0.0.1', '::1'])) {
            return true;
        }

        // 检查是否为私有 IP 范围
        if (filter_var($host, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
            return true;
        }

        return false;
    }

    /**
     * 解析HTML中的元数据
     */
    private function parseMetadata(string $html, string $baseUrl): array
    {
        $result = [
            'title' => '',
            'description' => '',
            'cover_image' => '',
        ];

        // 提取 Open Graph 标签 - 使用更灵活的正则匹配各种属性顺序
        // og:title
        if (preg_match('/<meta[^>]*property=["\']og:title["\'][^>]*content=["\']([^"\']+)["\']/i', $html, $matches)) {
            $result['title'] = $matches[1];
        } elseif (preg_match('/<meta[^>]*content=["\']([^"\']+)["\'][^>]*property=["\']og:title["\']/i', $html, $matches)) {
            $result['title'] = $matches[1];
        }

        // 降级到 title 标签
        if (empty($result['title'])) {
            if (preg_match('/<title[^>]*>([^<]+)<\/title>/i', $html, $matches)) {
                $result['title'] = trim($matches[1]);
            }
        }

        // og:description
        if (preg_match('/<meta[^>]*property=["\']og:description["\'][^>]*content=["\']([^"\']+)["\']/i', $html, $matches)) {
            $result['description'] = $matches[1];
        } elseif (preg_match('/<meta[^>]*content=["\']([^"\']+)["\'][^>]*property=["\']og:description["\']/i', $html, $matches)) {
            $result['description'] = $matches[1];
        }

        // 降级到 name=description
        if (empty($result['description'])) {
            if (preg_match('/<meta[^>]*name=["\']description["\'][^>]*content=["\']([^"\']+)["\']/i', $html, $matches)) {
                $result['description'] = $matches[1];
            } elseif (preg_match('/<meta[^>]*content=["\']([^"\']+)["\'][^>]*name=["\']description["\']/i', $html, $matches)) {
                $result['description'] = $matches[1];
            }
        }

        // og:image
        if (preg_match('/<meta[^>]*property=["\']og:image["\'][^>]*content=["\']([^"\']+)["\']/i', $html, $matches)) {
            $result['cover_image'] = $this->resolveUrl($matches[1], $baseUrl);
        } elseif (preg_match('/<meta[^>]*content=["\']([^"\']+)["\'][^>]*property=["\']og:image["\']/i', $html, $matches)) {
            $result['cover_image'] = $this->resolveUrl($matches[1], $baseUrl);
        }

        // 降级方案: twitter:image
        if (empty($result['cover_image'])) {
            if (preg_match('/<meta[^>]*name=["\']twitter:image["\'][^>]*content=["\']([^"\']+)["\']/i', $html, $matches)) {
                $result['cover_image'] = $this->resolveUrl($matches[1], $baseUrl);
            } elseif (preg_match('/<meta[^>]*content=["\']([^"\']+)["\'][^>]*name=["\']twitter:image["\']/i', $html, $matches)) {
                $result['cover_image'] = $this->resolveUrl($matches[1], $baseUrl);
            }
        }

        // 降级方案: 尝试获取网站图标作为封面
        if (empty($result['cover_image'])) {
            if (preg_match('/<link[^>]*rel=["\'](?:shortcut )?icon["\'][^>]*href=["\']([^"\']+)["\']/i', $html, $matches)) {
                $result['cover_image'] = $this->resolveUrl($matches[1], $baseUrl);
            } elseif (preg_match('/<link[^>]*href=["\']([^"\']+)["\'][^>]*rel=["\'](?:shortcut )?icon["\']/i', $html, $matches)) {
                $result['cover_image'] = $this->resolveUrl($matches[1], $baseUrl);
            }
        }

        return $result;
    }

    /**
     * 相对路径转绝对路径
     */
    private function resolveUrl(string $url, string $baseUrl): string
    {
        // 已经是绝对 URL，直接返回
        if (str_starts_with($url, 'http://') || str_starts_with($url, 'https://')) {
            return $url;
        }

        // 协议相对 URL (//example.com/image.png)
        if (str_starts_with($url, '//')) {
            $parsed = parse_url($baseUrl);
            $scheme = $parsed['scheme'] ?? 'https';
            return $scheme . ':' . $url;
        }

        // 根相对路径 (/image.png) -> 转为绝对路径
        if (str_starts_with($url, '/')) {
            $parsed = parse_url($baseUrl);
            $scheme = $parsed['scheme'] ?? 'https';
            $host = $parsed['host'] ?? '';
            $port = isset($parsed['port']) ? ':' . $parsed['port'] : '';
            return $scheme . '://' . $host . $port . $url;
        }

        // 相对路径 (image.png) -> 需要处理路径拼接
        $parsed = parse_url($baseUrl);
        $scheme = $parsed['scheme'] ?? 'https';
        $host = $parsed['host'] ?? '';
        $port = isset($parsed['port']) ? ':' . $parsed['port'] : '';
        $path = dirname($parsed['path'] ?? '/');
        return $scheme . '://' . $host . $port . '/' . $path . '/' . $url;
    }
}
