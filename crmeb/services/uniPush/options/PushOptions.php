<?php

declare(strict_types=1);


namespace crmeb\services\uniPush\options;

use crmeb\services\uniPush\helper\Str;
use crmeb\services\uniPush\OptionsBase;

/**
 * Class PushOptions.
 */
class PushOptions extends OptionsBase
{
    /**
     * @var string
     */
    public $requestId;

    /**
     * @var array
     */
    public $settings = [];

    /**
     * @var array
     */
    public $audience = [];

    /**
     * @var array
     */
    public $pushMessage = [];

    /**
     * @var array
     */
    public $pushChannel = [];

    /**
     * PushOptions constructor.
     */
    public function __construct(string $requestId = '', array $settings = [], array $audience = [], array $pushMessage = [])
    {
        $this->requestId   = $requestId;
        $this->settings    = $settings;
        $this->audience    = $audience;
        $this->pushMessage = $pushMessage;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        $publicData = get_object_vars($this);
        $data       = [];
        foreach ($publicData as $key => $value) {
            if ($value) {
                $data[Str::snake($key)] = $value;
            }
        }
        return $data;
    }

    /**
     * @return $this
     */
    public function setPushMessage(PushMessageOptions $options)
    {
        $this->pushMessage = $options->toArray();
        return $this;
    }

    public function setPushChannel(AndroidOptions $androidOptions, IosOptions $iosOptions)
    {
        return [
            'ios'     => $iosOptions->toArray(),
            'android' => $androidOptions->toArray(),
        ];
    }

    /**
     * @param mixed ...$clientId
     * @return $this
     */
    public function setAudience(...$clientId)
    {
        $this->audience = ['cid' => $clientId];
        return $this;
    }
}
