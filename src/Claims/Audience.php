<?php

declare(strict_types=1);
/**
 * This file is part of youbuwei/hyperf-jwt
 *
 * @link     https://github.com/youbuwei/hyperf-jwt
 * @contact  youbuwei@yahoo.com
 * @license  https://github.com/youbuwei/hyperf-jwt/blob/master/LICENSE
 */
namespace Youbuwei\HyperfJwt\Claims;

class Audience extends AbstractClaim
{
    protected $name = 'aud';

    public function validate(bool $ignoreExpired = false): bool
    {
        return true;
    }
}
