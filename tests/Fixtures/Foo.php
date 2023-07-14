<?php

declare(strict_types=1);
/**
 * This file is part of youbuwei/hyperf-jwt
 *
 * @link     https://github.com/youbuwei/hyperf-jwt
 * @contact  youbuwei@yahoo.com
 * @license  https://github.com/youbuwei/hyperf-jwt/blob/master/LICENSE
 */
namespace HyperfTest\Fixtures;

use Youbuwei\HyperfJwt\Claims\AbstractClaim;

class Foo extends AbstractClaim
{
    protected $name = 'foo';

    public function validate(bool $ignoreExpired = false): bool
    {
        return true;
    }
}
