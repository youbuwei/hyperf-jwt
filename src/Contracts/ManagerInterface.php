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

use Youbuwei\HyperfJwt\Blacklist;
use Youbuwei\HyperfJwt\Payload;
use Youbuwei\HyperfJwt\Token;

interface ManagerInterface
{
    /**
     * Encode a Payload and return the Token.
     */
    public function encode(Payload $payload): Token;

    /**
     * Decode a Token and return the Payload.
     *
     * @throws \Youbuwei\HyperfJwt\Exceptions\TokenBlacklistedException
     */
    public function decode(Token $token, bool $checkBlacklist = true): Payload;

    /**
     * Refresh a Token and return a new Token.
     *
     * @throws \Youbuwei\HyperfJwt\Exceptions\TokenBlacklistedException
     * @throws \Youbuwei\HyperfJwt\Exceptions\JwtException
     */
    public function refresh(Token $token, bool $forceForever = false): Token;

    /**
     * Invalidate a Token by adding it to the blacklist.
     *
     * @throws \Youbuwei\HyperfJwt\Exceptions\JwtException
     */
    public function invalidate(Token $token, bool $forceForever = false): bool;
}
