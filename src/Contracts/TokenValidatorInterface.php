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

interface TokenValidatorInterface extends ValidatorInterface
{
    /**
     * Perform some checks on the value.
     */
    public function check(string $value): string;

    /**
     * Helper function to return a boolean.
     */
    public function isValid(string $value): bool;
}
