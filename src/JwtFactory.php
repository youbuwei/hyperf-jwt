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

use Hyperf\Contract\ConfigInterface;
use Youbuwei\HyperfJwt\Contracts\JwtFactoryInterface;

class JwtFactory implements JwtFactoryInterface
{
    protected $lockSubject = true;

    public function __construct(ConfigInterface $config)
    {
        $this->lockSubject = (bool) $config->get('jwt.lock_subject');
    }

    public function make(): Jwt
    {
        return \Hyperf\Support\make(Jwt::class)->setLockSubject($this->lockSubject);
    }
}
