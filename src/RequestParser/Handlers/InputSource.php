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

class InputSource implements ParserContract
{
    use KeyTrait;

    public function parse(ServerRequestInterface $request): ?string
    {
        $data = \Hyperf\Collection\data_get(
            is_array($data = $request->getParsedBody()) ? $data : [],
            $this->key
        );
        return empty($data) === null ? null : (string) $data;
    }
}
