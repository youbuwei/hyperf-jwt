<?php

declare(strict_types=1);
/**
 * This file is part of youbuwei/hyperf-jwt
 *
 * @link     https://github.com/youbuwei/hyperf-jwt
 * @contact  youbuwei@yahoo.com
 * @license  https://github.com/youbuwei/hyperf-jwt/blob/master/LICENSE
 */
namespace Youbuwei\HyperfJwt\Contracts\RequestParser;

use Psr\Http\Message\ServerRequestInterface;

interface HandlerInterface
{
    /**
     * Parse the request.
     *
     * @param \Hyperf\HttpServer\Request|\Psr\Http\Message\ServerRequestInterface $request
     */
    public function parse(ServerRequestInterface $request): ?string;
}
