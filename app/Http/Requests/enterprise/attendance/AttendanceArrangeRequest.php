<?php

declare(strict_types=1);


namespace App\Http\Requests\enterprise\attendance;

use App\Http\Requests\ApiValidate;

/**
 * 考勤排班
 * Class AttendanceArrangeRequest.
 */
class AttendanceArrangeRequest extends ApiValidate
{
    /**
     * 场景.
     * @var \string[][]
     */
    protected $scene = [
        'store' => ['date', 'groups'],
    ];
    /**
     * 自动.
     * @var bool
     */
    // public $authValidate = true;

    /**
     * @return array|string[]
     */
    public function rules(): array
    {
        return [
            'date'   => 'required|date',
            'groups' => 'required|array',
        ];
    }

    /**
     * @return string[]
     */
    public function message(): array
    {
        return [
            'date.required'   => '请选择考勤时间',
            'date.date'       => '请选择正确的考勤时间',
            'groups.required' => '请选择考勤组',
            'groups.array'    => '请选择正确的考勤组',
        ];
    }
}
