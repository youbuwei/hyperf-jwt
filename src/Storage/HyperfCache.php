<?php

declare(strict_types=1);
/**
 * This file is part of youbuwei/hyperf-jwt
 *
 * @link     https://github.com/youbuwei/hyperf-jwt
 * @contact  youbuwei@yahoo.com
 * @license  https://github.com/youbuwei/hyperf-jwt/blob/master/LICENSE
 */
namespace Youbuwei\HyperfJwt\Storage;

use Psr\SimpleCache\CacheInterface;
use Youbuwei\HyperfJwt\Contracts\StorageInterface;

class HyperfCache implements StorageInterface
{
    /**
     * The cache repository contract.
     *
     * @var \Psr\SimpleCache\CacheInterface
     */
    protected $cache;

    /**
     * The used cache tag.
     *
     * @var string
     */
    protected $tag;

    /**
     * Constructor.
     */
    public function __construct(CacheInterface $cache, string $tag)
    {
        $this->cache = $cache;
        $this->tag = $tag;
    }

    public function add(string $key, $value, int $ttl)
    {
        $this->cache->set($this->resolveKey($key), $value, $ttl);
    }

    public function forever(string $key, $value)
    {
        $this->cache->set($this->resolveKey($key), $value);
    }

    public function get(string $key)
    {
        return $this->cache->get($this->resolveKey($key));
    }

    public function destroy(string $key): bool
    {
        return $this->cache->delete($this->resolveKey($key));
    }

    public function flush(): void
    {
        method_exists($cache = $this->cache, 'clearPrefix')
            ? $cache->clearPrefix($this->tag)
            : $cache->clear();
    }

    protected function cache(): CacheInterface
    {
        return $this->cache;
    }

    protected function resolveKey(string $key)
    {
        return $this->tag . '.' . $key;
    }
}
