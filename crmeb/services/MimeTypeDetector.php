<?php

declare(strict_types=1);


namespace crmeb\services;

use League\MimeTypeDetection\GeneratedExtensionToMimeTypeMap;
use League\MimeTypeDetection\OverridingExtensionToMimeTypeMap;

class MimeTypeDetector
{
    public $detector;

    // 思维导图格式的 MIME 类型映射
    private array $mindmap_mime_types = [
        'xmind'    => ['application/vnd.xmind.workbook', 'application/x-xmind'],
        'mmap'     => 'application/vnd.mindjet.mindmanager',
        'mm'       => 'application/x-freemind',
        'mindnode' => 'application/vnd.mindnode.mindnode',
        'itmz'     => 'application/vnd.ithoughts.thoughts',
    ];

    public function __construct()
    {
        // 创建自定义扩展映射（合并默认和自定义）
        $extensionMap = new GeneratedExtensionToMimeTypeMap();
        return new OverridingExtensionToMimeTypeMap($extensionMap, $this->mindmap_mime_types);
    }

    public function getMimeTypes(): array
    {
        $extensionMap = new GeneratedExtensionToMimeTypeMap();
        return array_merge($this->mindmap_mime_types, $extensionMap::MIME_TYPES_FOR_EXTENSIONS);
    }
}
