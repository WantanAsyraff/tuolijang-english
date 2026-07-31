<?php

declare(strict_types=1);


namespace crmeb\utils;

use Illuminate\Support\Carbon;

class Date
{
    /**
     * 获取环比时间.
     * @return string[]
     * @example
     * ```
     * Date::ringRatioTime('2023/01/01-2023/01/31');
     * ```
     */
    public static function ringRatioTime(string $originTime = '', string $format = 'Y/m/d H:i:s'): array
    {
        $tz = config('app.timezone');
        if (! $originTime) {
            $todayObj   = Carbon::today($tz);
            $originTime = $todayObj->startOfMonth()->format($format) . '-' . (clone $todayObj)->endOfMonth()->format($format);
        }

        $time        = explode('-', $originTime);
        $originStart = Carbon::parse($time[0])->timezone($tz);
        $originEnt   = Carbon::parse($time[1])->timezone($tz);

        if ($time[0] != $time[1]) {
            $day = $originEnt->diffInDays($originStart);
            if ($day < 0) {
                return [$originTime, $originTime];
            }
        } else {
            $day = 1;
        }

        return [
            $originStart->format($format) . '-' . $originEnt->endOfDay()->format($format),
            (clone $originStart)->subDays($day + 1)->format($format) . '-' .
            (clone $originEnt)->subDays($day + 1)->endOfDay()->format($format),
        ];
    }
}
