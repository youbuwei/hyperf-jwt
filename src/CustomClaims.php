<?php

declare(strict_types=1);
/**
 * This file is part of youbuwei/hyperf-jwt
 *
 * @link     https://github.com/youbuwei/hyperf-jwt
 * @contact  youbuwei@yahoo.com
 * @license  https://github.com/youbuwei/hyperf-jwt/blob/master/LICENSE
 */
namespace Youbuwei\HyperfJwt;

trait CustomClaims
{
    /**
     * Custom claims.
     *
     * @var array
     */
    protected $customClaims = [];

    /**
     * Set the custom claims.
     *
     * @return $this
     */
    public function setCustomClaims(array $customClaims)
    {
        $this->customClaims = $customClaims;

        return $this;
    }

    /**
     * Get the custom claims.
     */
    public function getCustomClaims(): array
    {
        return $this->customClaims;
    }
}
