<?php

declare(strict_types=1);


namespace crmeb\services\wps\options;

use crmeb\interfaces\OptionsInterface;
use crmeb\traits\OptionsTrait;

/**
 * Class OfficeFileNewOptions.
 */
class OfficeFileNewOptions implements OptionsInterface
{
    use OptionsTrait;

    /**
     * 访问地址
     * @var string
     */
    public $redirectUrl;

    /**
     * 用户ID.
     * @var string
     */
    public $userId;

    /**
     * OfficeFileNewOptions constructor.
     */
    public function __construct(?string $redirectUrl = null, ?string $userId = null)
    {
        $this->redirectUrl = $redirectUrl;
        $this->userId      = $userId;
    }
}
