<?php

declare(strict_types=1);


namespace App\Mcp\Tools\Personnel;

use App\Http\Service\Frame\FrameAssistService;
use App\Mcp\Tools\Abstract\BaseTool;

/**
 * 当前登录用户身份工具
 * Class WhoAmITool.
 */
class WhoAmITool extends BaseTool
{
    public function getDescription(): string
    {
        return '获取当前 MCP 登录用户的身份信息，用于让 AI 知道“我是谁”';
    }

    public function getInputSchema(): array
    {
        return [
            'type'       => 'object',
            'properties' => new \stdClass(),
        ];
    }

    public function execute(array $arguments): array
    {
        $userInfo = $this->getUserInfo();
        $frames   = $this->getSafeFrames((string) ($userInfo['uid'] ?? ''));

        return [
            'id'             => $this->getUserDbId(),
            'uid'            => $userInfo['uid'] ?? '',
            'name'           => $userInfo['name'] ?? '',
            'avatar'         => $userInfo['avatar'] ?? '',
            'phone'          => $userInfo['phone'] ?? '',
            'job_id'         => (int) ($userInfo['job'] ?? 0),
            'is_admin'       => $this->isAdmin(),
            'main_frame_id'  => $this->getMainFrameId($frames),
            'frame_ids'      => array_column($frames, 'id'),
            'frames'         => $frames,
        ];
    }

    private function getSafeFrames(string $uuid): array
    {
        if ($uuid === '') {
            return [];
        }

        $frames = app(FrameAssistService::class)->getUserFrames($uuid);
        $frames = $frames ? (is_array($frames) ? $frames : $frames->toArray()) : [];

        return array_values(array_map(function (array $item) {
            $frame = $item['frame'] ?? [];

            return [
                'id'          => (int) ($frame['id'] ?? $item['frame_id'] ?? 0),
                'name'        => $frame['name'] ?? '',
                'is_main'     => (bool) ($item['is_mastart'] ?? false),
                'is_manager'  => (bool) ($item['is_admin'] ?? false),
            ];
        }, $frames));
    }

    private function getMainFrameId(array $frames): int
    {
        foreach ($frames as $frame) {
            if ($frame['is_main'] ?? false) {
                return (int) $frame['id'];
            }
        }

        return (int) ($frames[0]['id'] ?? 0);
    }
}
