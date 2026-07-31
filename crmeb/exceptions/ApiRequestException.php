<?php

declare(strict_types=1);


namespace crmeb\exceptions;

use crmeb\services\ApiResponseService;
use Illuminate\Http\JsonResponse;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * Class ApiRequestException.
 */
class ApiRequestException extends \RuntimeException
{
    /**
     * @var int
     */
    protected $statusCode;

    public function __construct($message = '', $code = 0, ?\Throwable $previous = null, int $statusCode = 200)
    {
        parent::__construct($message, $code, $previous);
        $this->statusCode = $statusCode;
    }

    /**
     * @return JsonResponse
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function render()
    {
        /** @var ApiResponseService $response */
        $response = app()->get(ApiResponseService::class);
        return $response->httpStatus($this->code)->make($this->code, $this->getMessage(), $this->getTrace());
    }
}
