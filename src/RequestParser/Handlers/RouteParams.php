<?php

declare(strict_types=1);
/**
 * This file is part of youbuwei/hyperf-jwt
 *
 * @link     https://github.com/youbuwei/hyperf-jwt
 * @contact  youbuwei@yahoo.com
 * @license  https://github.com/youbuwei/hyperf-jwt/blob/master/LICENSE
 */
namespace Youbuwei\HyperfJwt\RequestParser\Handlers;

use Psr\Http\Message\ServerRequestInterface;
use Youbuwei\HyperfJwt\Contracts\RequestParser\HandlerInterface as ParserContract;

class RouteParams implements ParserContract
{
    use KeyTrait;

    /**
     * @param \Hyperf\HttpServer\Request|\Psr\Http\Message\ServerRequestInterface $request
     */
    public function parse(ServerRequestInterface $request): ?string
    {
        return $request->route($this->key);
    }
}
