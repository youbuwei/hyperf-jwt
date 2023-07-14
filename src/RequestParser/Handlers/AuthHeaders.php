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

class AuthHeaders implements ParserContract
{
    /**
     * The header name.
     *
     * @var string
     */
    protected string $header = 'authorization';

    /**
     * The header prefix.
     *
     * @var string
     */
    protected $prefix = 'bearer';

    public function parse(ServerRequestInterface $request): ?string
    {
        $header = $request->getHeaderLine($this->header);

        if ($header and preg_match('/' . $this->prefix . '\s*(\S+)\b/i', $header, $matches)) {
            return $matches[1];
        }

        return null;
    }

    /**
     * Set the header name.
     *
     * @return $this
     */
    public function setHeaderName(string $headerName)
    {
        $this->header = $headerName;

        return $this;
    }

    /**
     * Set the header prefix.
     *
     * @return $this
     */
    public function setHeaderPrefix(string $headerPrefix)
    {
        $this->prefix = $headerPrefix;

        return $this;
    }
}
