<?php

declare(strict_types=1);


namespace crmeb\services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * Open-Meteo 天气服务.
 */
class WeatherService
{
    protected Client $client;

    // 免费IP定位服务
    protected const IP_API_URL = 'http://ip-api.com';

    // Open-Meteo 天气API（完全免费，无需API Key）
    protected const WEATHER_API_URL = 'https://api.open-meteo.com/v1/forecast';

    public function __construct()
    {
        $this->client = new Client(['timeout' => 10]);
    }

    /**
     * 根据IP获取地理位置和天气.
     */
    public function getWeatherByIp(string $ip): Collection
    {
        // 内网IP返回默认天气
        if ($this->isPrivateIp($ip)) {
            return $this->getDefaultWeather();
        }

        // 获取IP地理位置
        $location = $this->getLocationByIp($ip);
        if (!$location) {
            return $this->getDefaultWeather();
        }

        // 获取天气信息
        return $this->getWeather((float) $location['lat'], (float) $location['lon'], $location['location'] ?? '');
    }

    /**
     * 根据IP获取经纬度（使用ip-api.com）.
     */
    public function getLocationByIp(string $ip): ?array
    {
        $cacheKey = 'weather:ip_location:' . $ip;
        $cached = Cache::get($cacheKey);
        if ($cached) {
            return $cached;
        }

        try {
            $response = $this->client->get(self::IP_API_URL . '/json/' . $ip);
            $data = json_decode($response->getBody()->getContents(), true);

            if (($data['status'] ?? '') !== 'success') {
                Log::error('WeatherService getLocationByIp failed', ['ip' => $ip, 'data' => $data]);
                return null;
            }

            $location = [
                'lat' => (string) $data['lat'],
                'lon' => (string) $data['lon'],
                'location' => ($data['city'] ?? '') . ', ' . ($data['regionName'] ?? '') . ', ' . ($data['country'] ?? ''),
            ];

            Cache::put($cacheKey, $location, now()->addHours(24));

            return $location;
        } catch (GuzzleException $e) {
            Log::error('WeatherService getLocationByIp error', ['ip' => $ip, 'error' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * 获取实时天气（使用Open-Meteo免费API）.
     */
    public function getWeather(float $lat, float $lon, string $location = ''): Collection
    {
        $cacheKey = 'weather:current:' . round($lat, 2) . ':' . round($lon, 2);
        $cached = Cache::get($cacheKey);
        if ($cached) {
            return collect($cached);
        }

        try {
            $response = $this->client->get(self::WEATHER_API_URL, [
                'query' => [
                    'latitude' => $lat,
                    'longitude' => $lon,
                    'current_weather' => true,
                    'temperature_unit' => 'celsius',
                    'wind_speed_unit' => 'kmh',
                ],
            ]);

            $data = json_decode($response->getBody()->getContents(), true);

            if (!isset($data['current_weather'])) {
                Log::error('WeatherService getWeather failed', ['lat' => $lat, 'lon' => $lon, 'data' => $data]);
                return $this->getDefaultWeather();
            }
            $now = $data['current_weather'];

            // WMO 天气代码转换
            $weatherText = $this->convertWeatherCode((int) ($now['weathercode'] ?? 0));

            $result = [
                'location' => $location,
                'weather' => $weatherText,
                'weatherCode' => $now['weathercode'],
                'temp' => (int) round($now['temperature'] ?? 0),
                'feelsLike' => (int) round($now['temperature'] ?? 0), // Open-Meteo无体感温度
                'humidity' => 0, // Open-Meteo基础版无湿度
                'windSpeed' => (int) round($now['windspeed'] ?? 0),
                'updateTime' => date('Y-m-d H:i:s'),
            ];

            Cache::put($cacheKey, $result, now()->addMinutes(15));

            return collect($result);
        } catch (GuzzleException $e) {
            Log::error('WeatherService getWeather error', ['lat' => $lat, 'lon' => $lon, 'error' => $e->getMessage()]);
            return $this->getDefaultWeather();
        }
    }

    /**
     * WMO 天气代码转换为文字描述.
     * @see https://open-meteo.com/en/docs#weathervariables
     */
    protected function convertWeatherCode(int $code): string
    {
        $weatherCodes = [
            // 0: 晴朗
            0 => '晴',
            // 1-3: 主要是晴朗/多云
            1 => '晴间多云',
            2 => '多云',
            3 => '阴',
            // 45-48: 雾和冰雾
            45 => '雾',
            48 => '雾凇',
            // 51-57: 毛毛雨
            51 => '小毛毛雨',
            53 => '中毛毛雨',
            55 => '大毛毛雨',
            56 => '冻毛毛雨',
            57 => '强冻毛毛雨',
            // 61-67: 降雨
            61 => '小雨',
            63 => '中雨',
            65 => '大雨',
            66 => '冻雨',
            67 => '强冻雨',
            // 71-77: 降雪
            71 => '小雪',
            73 => '中雪',
            75 => '大雪',
            77 => '雪粒',
            // 80-82: 阵雨
            80 => '小阵雨',
            81 => '中阵雨',
            82 => '大阵雨',
            // 85-86: 阵雪
            85 => '小阵雪',
            86 => '大阵雪',
            // 95-99: 雷暴
            95 => '雷暴',
            96 => '雷暴伴小冰雹',
            99 => '雷暴伴大冰雹',
        ];

        return $weatherCodes[$code] ?? '未知';
    }

    /**
     * 获取默认天气.
     */
    protected function getDefaultWeather(): Collection
    {
        return collect([
            'location' => '未知',
            'weather' => '未知',
            'weatherCode' => -1,
            'temp' => 0,
            'feelsLike' => 0,
            'humidity' => 0,
            'windSpeed' => 0,
            'updateTime' => date('Y-m-d H:i:s'),
        ]);
    }

    /**
     * 判断是否为内网IP.
     */
    protected function isPrivateIp(string $ip): bool
    {
        return !filter_var(
            $ip,
            FILTER_VALIDATE_IP,
            FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE
        );
    }
}
