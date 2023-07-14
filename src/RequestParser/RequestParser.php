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

use Psr\Http\Message\ServerRequestInterface;
use Youbuwei\HyperfJwt\Contracts\RequestParser\RequestParserInterface;

class RequestParser implements RequestParserInterface
{
    /**
     * @var \Youbuwei\HyperfJwt\Contracts\RequestParser\HandlerInterface[]
     */
    private $handlers;

    /**
     * @param \Youbuwei\HyperfJwt\Contracts\RequestParser\HandlerInterface[] $handlers
     */
    public function __construct(array $handlers = [])
    {
        $this->handlers = $handlers;
    }

    public function getHandlers(): array
    {
        return $this->handlers;
    }

    public function setHandlers(array $handlers)
    {
        $this->handlers = $handlers;

        return $this;
    }

    public function parseToken(ServerRequestInterface $request): ?string
    {
        foreach ($this->handlers as $handler) {
            if ($token = $handler->parse($request)) {
                return $token;
            }
        }
        return null;
    }

    public function hasToken(ServerRequestInterface $request): bool
    {
        return $this->parseToken($request) !== null;
    }
}
