<?php

declare(strict_types=1);
/**
 * This file is part of youbuwei/hyperf-jwt
 *
 * @link     https://github.com/youbuwei/hyperf-jwt
 * @contact  youbuwei@yahoo.com
 * @license  https://github.com/youbuwei/hyperf-jwt/blob/master/LICENSE
 */
namespace Youbuwei\HyperfJwt\Contracts;

use Youbuwei\HyperfJwt\Claims\Collection;

interface PayloadValidatorInterface
{
    /**
     * Perform some checks on the value.
     * @throws \Youbuwei\HyperfJwt\Exceptions\TokenInvalidException
     * @throws \Youbuwei\HyperfJwt\Exceptions\TokenExpiredException
     */
    public function check(Collection $value, bool $ignoreExpired = false): Collection;

    /**
     * Helper function to return a boolean.
     */
    public function isValid(Collection $value, bool $ignoreExpired = false): bool;
}
