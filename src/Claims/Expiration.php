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

use Youbuwei\HyperfJwt\Exceptions\TokenExpiredException;

class Expiration extends AbstractClaim
{
    use DatetimeTrait;

    protected $name = 'exp';

    public function validate(bool $ignoreExpired = false): bool
    {
        if (! $ignoreExpired and $this->isPast($this->getValue())) {
            throw new TokenExpiredException('Token has expired');
        }
        return true;
    }
}
