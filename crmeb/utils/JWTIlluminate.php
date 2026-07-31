<?php

declare(strict_types=1);


namespace crmeb\utils;

use Illuminate\Contracts\Auth\UserProvider;
use Tymon\JWTAuth\Providers\Auth\Illuminate;

/**
 * Class JWTIlluminate.
 */
class JWTIlluminate extends Illuminate
{
    /**
     * Set the user provider used by the guard.
     *
     * @return $this
     */
    public function setProvider(UserProvider $provider)
    {
        $this->auth->setProvider($provider);
        unset($provider);
        return $this;
    }
}
