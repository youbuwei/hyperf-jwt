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

interface StorageInterface
{
    /**
     * @param mixed $value
     */
    public function add(string $key, $value, int $ttl);

    /**
     * @param mixed $value
     */
    public function forever(string $key, $value);

    /**
     * @return mixed
     */
    public function get(string $key);

    public function destroy(string $key): bool;

    public function flush(): void;
}
