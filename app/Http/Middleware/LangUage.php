<?php

declare(strict_types=1);


namespace App\Http\Middleware;

use crmeb\interfaces\ApiMiddlewareInterface;
use crmeb\traits\MiddlewareTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Tymon\JWTAuth\Http\Middleware\BaseMiddleware;

/**
 * 语言包切换
 * Class LangUage.
 */
class LangUage extends BaseMiddleware implements ApiMiddlewareInterface
{
    use MiddlewareTrait;

    /**
     * @return mixed|void
     */
    public function before(Request $request)
    {
        $lang = strtolower((string) ($request->cookies->get('language') ?: $request->header('laravel_lang')));
        $lang = match ($lang) {
            'zh', 'zh_cn', 'zh-cn', 'zh-hans' => 'zh-cn',
            'en', 'en_us', 'en-us', 'en-gb' => 'en',
            default => $lang,
        };

        if ($lang && in_array($lang, array_keys(config('app.locales')), true)) {
            App::setLocale($lang);
        } else {
            App::setLocale(config('app.locale'));
        }
    }

    public function after($response)
    {
        // TODO: Implement after() method.
    }
}
