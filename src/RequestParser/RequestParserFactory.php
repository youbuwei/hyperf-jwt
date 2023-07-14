<?php

declare(strict_types=1);
/**
 * This file is part of youbuwei/hyperf-jwt
 *
 * @link     https://github.com/youbuwei/hyperf-jwt
 * @contact  youbuwei@yahoo.com
 * @license  https://github.com/youbuwei/hyperf-jwt/blob/master/LICENSE
 */
namespace Youbuwei\HyperfJwt\RequestParser;

use Youbuwei\HyperfJwt\RequestParser\Handlers\AuthHeaders;
use Youbuwei\HyperfJwt\RequestParser\Handlers\Cookies;
use Youbuwei\HyperfJwt\RequestParser\Handlers\InputSource;
use Youbuwei\HyperfJwt\RequestParser\Handlers\QueryString;
use Youbuwei\HyperfJwt\RequestParser\Handlers\RouteParams;

class RequestParserFactory
{
    public function __invoke()
    {
        return \Hyperf\Support\make(RequestParser::class)->setHandlers([
            new AuthHeaders(),
            new QueryString(),
            new InputSource(),
            new RouteParams(),
            new Cookies(),
        ]);
    }
}
