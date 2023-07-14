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
use Psr\Container\ContainerInterface;
use Youbuwei\HyperfJwt\Claims\Factory as ClaimFactory;
use Youbuwei\HyperfJwt\Contracts\CodecInterface;
use Youbuwei\HyperfJwt\Exceptions\InvalidConfigException;
use Youbuwei\HyperfJwt\Storage\HyperfCache;

class ManagerFactory
{
    /**
     * @var array
     */
    private $config;

    public function __invoke(ContainerInterface $container)
    {
        $config = $container->get(ConfigInterface::class)->get('jwt');
        if (empty($config)) {
            throw new InvalidConfigException(sprintf('JWT config is not defined.'));
        }

        $this->config = $config;

        $codec = $this->resolveCodec();
        $blacklist = $this->resolveBlacklist();
        $claimFactory = $this->resolverClaimFactory();
        $payloadFactory = $this->resolverPayloadFactory($claimFactory);

        return \Hyperf\Support\make(Manager::class, compact('codec', 'blacklist', 'claimFactory', 'payloadFactory'))
            ->setBlacklistEnabled($this->config['blacklist_enabled']);
    }

    private function resolveCodec(): CodecInterface
    {
        $secret = base64_decode($this->config['secret'] ?? '');
        $algo = $this->config['algo'] ?? 'HS256';
        $keys = $this->config['keys'] ?? [];
        if (! empty($keys)) {
            $keys['passphrase'] = empty($keys['passphrase']) ? null : base64_decode($keys['passphrase']);
        }
        return \Hyperf\Support\make(Codec::class, compact('secret', 'algo', 'keys'));
    }

    private function resolveBlacklist(): Blacklist
    {
        $storageClass = $this->config['blacklist_storage'] ?? HyperfCache::class;
        $storage = \Hyperf\Support\make($storageClass, [
            'tag' => 'jwt.default',
        ]);
        $gracePeriod = $this->config['blacklist_grace_period'];
        $refreshTtl = $this->config['refresh_ttl'];

        return \Hyperf\Support\make(Blacklist::class, compact('storage', 'gracePeriod', 'refreshTtl'));
    }

    private function resolverClaimFactory(): ClaimFactory
    {
        $ttl = $this->config['ttl'];
        $refreshTtl = $this->config['refresh_ttl'];
        $leeway = $this->config['leeway'];

        return \Hyperf\Support\make(ClaimFactory::class, compact('ttl', 'refreshTtl', 'leeway'));
    }

    private function resolverPayloadFactory(ClaimFactory $claimFactory): PayloadFactory
    {
        return \Hyperf\Support\make(PayloadFactory::class, compact('claimFactory'))
            ->setTtl($this->config['ttl']);
    }
}
