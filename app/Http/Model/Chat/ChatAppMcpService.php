<?php

declare(strict_types=1);


namespace App\Http\Model\Chat;

use crmeb\basic\BaseModel;
use crmeb\traits\model\TimeDataTrait;

/**
 * Class ChatAppMcpService.
 */
class ChatAppMcpService extends BaseModel
{
    use TimeDataTrait;

    protected $primaryKey = 'id';

    protected $casts = [
        'id'         => 'integer',
        'status'     => 'integer',
        'is_default' => 'integer',
        'sort'       => 'integer',
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
    ];

    protected $table = 'chat_app_mcp_services';

    public function getHeadersAttribute($value): array
    {
        if ($value) {
            $headers = json_decode($value, true);
            return is_array($headers) ? $headers : [];
        }
        return [];
    }

    public function setHeadersAttribute($value): void
    {
        if (is_array($value)) {
            $this->attributes['headers'] = json_encode($value, JSON_UNESCAPED_UNICODE);
        } else {
            $this->attributes['headers'] = $value;
        }
    }

    public function getConfigJsonAttribute($value): ?array
    {
        if (empty($value)) {
            return null;
        }
        $config = json_decode($value, true);
        return is_array($config) ? $config : null;
    }

    public function setConfigJsonAttribute($value): void
    {
        if (is_array($value)) {
            $this->attributes['config_json'] = json_encode($value, JSON_UNESCAPED_UNICODE);
        } elseif (is_string($value) && $this->isJson($value)) {
            $this->attributes['config_json'] = $value;
        } else {
            $this->attributes['config_json'] = $value;
        }
    }

    private function isJson(string $str): bool
    {
        json_decode($str);
        return json_last_error() === JSON_ERROR_NONE;
    }
}
