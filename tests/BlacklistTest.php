<?php

declare(strict_types=1);
/**
 * This file is part of youbuwei/hyperf-jwt
 *
 * @link     https://github.com/youbuwei/hyperf-jwt
 * @contact  youbuwei@yahoo.com
 * @license  https://github.com/youbuwei/hyperf-jwt/blob/master/LICENSE
 */
namespace HyperfTest;

use Youbuwei\HyperfJwt\Blacklist;
use Youbuwei\HyperfJwt\Claims\Collection;
use Youbuwei\HyperfJwt\Claims\Expiration;
use Youbuwei\HyperfJwt\Claims\IssuedAt;
use Youbuwei\HyperfJwt\Claims\Issuer;
use Youbuwei\HyperfJwt\Claims\JwtId;
use Youbuwei\HyperfJwt\Claims\NotBefore;
use Youbuwei\HyperfJwt\Claims\Subject;
use Youbuwei\HyperfJwt\Contracts\StorageInterface;
use Youbuwei\HyperfJwt\Payload;

/**
 * @internal
 * @coversNothing
 */
class BlacklistTest extends AbstractTestCase
{
    /**
     * @var \Youbuwei\HyperfJwt\Contracts\StorageInterface|\Mockery\MockInterface
     */
    protected $storage;

    /**
     * @var \Youbuwei\HyperfJwt\Blacklist
     */
    protected $blacklist;

    public function setUp(): void
    {
        parent::setUp();

        $this->storage = \Mockery::mock(StorageInterface::class);
        $this->blacklist = new Blacklist($this->storage, 0, 3600 * 24 * 14);
    }

    /** @test */
    public function itShouldAddAValidTokenToTheBlacklist()
    {
        $claims = [
            new Subject(1),
            new Issuer('http://example.com'),
            new Expiration($this->testNowTimestamp + 3600),
            new NotBefore($this->testNowTimestamp),
            new IssuedAt($this->testNowTimestamp),
            new JwtId('foo'),
        ];

        $collection = Collection::make($claims);

        $payload = new Payload($collection);

        $refreshTtl = 1209660;

        $this->storage->shouldReceive('get')
            ->with('foo')
            ->once()
            ->andReturn([]);

        $this->storage->shouldReceive('add')
            ->with('foo', ['valid_until' => $this->testNowTimestamp], $refreshTtl + 60)
            ->once();

        $this->assertTrue($this->blacklist->setRefreshTtl($refreshTtl)->add($payload));
    }

    /** @test */
    public function itShouldAddATokenWithNoExpToTheBlacklistForever()
    {
        $claims = [
            new Subject(1),
            new Issuer('http://example.com'),
            new NotBefore($this->testNowTimestamp),
            new IssuedAt($this->testNowTimestamp),
            new JwtId('foo'),
        ];
        $collection = Collection::make($claims);

        $payload = new Payload($collection);

        $this->storage->shouldReceive('forever')->with('foo', 'forever')->once();

        $this->assertTrue($this->blacklist->add($payload));
    }

    /** @test */
    public function itShouldReturnTrueWhenAddingAnExpiredTokenToTheBlacklist()
    {
        $claims = [
            new Subject(1),
            new Issuer('http://example.com'),
            new Expiration($this->testNowTimestamp - 3600),
            new NotBefore($this->testNowTimestamp),
            new IssuedAt($this->testNowTimestamp),
            new JwtId('foo'),
        ];
        $collection = Collection::make($claims);

        $payload = new Payload($collection, true);

        $refreshTtl = 1209660;

        $this->storage->shouldReceive('get')
            ->with('foo')
            ->once()
            ->andReturn([]);

        $this->storage->shouldReceive('add')
            ->with('foo', ['valid_until' => $this->testNowTimestamp], $refreshTtl + 60)
            ->once();

        $this->assertTrue($this->blacklist->setRefreshTtl($refreshTtl)->add($payload));
    }

    /** @test */
    public function itShouldReturnTrueEarlyWhenAddingAnItemAndItAlreadyExists()
    {
        $claims = [
            new Subject(1),
            new Issuer('http://example.com'),
            new Expiration($this->testNowTimestamp - 3600),
            new NotBefore($this->testNowTimestamp),
            new IssuedAt($this->testNowTimestamp),
            new JwtId('foo'),
        ];
        $collection = Collection::make($claims);

        $payload = new Payload($collection, true);

        $refreshTtl = 1209660;

        $this->storage->shouldReceive('get')
            ->with('foo')
            ->once()
            ->andReturn(['valid_until' => $this->testNowTimestamp]);

        $this->storage->shouldReceive('add')
            ->with('foo', ['valid_until' => $this->testNowTimestamp], $refreshTtl + 60)
            ->never();

        $this->assertTrue($this->blacklist->setRefreshTtl($refreshTtl)->add($payload));
    }

    /** @test */
    public function itShouldCheckWhetherATokenHasBeenBlacklisted()
    {
        $claims = [
            new Subject(1),
            new Issuer('http://example.com'),
            new Expiration($this->testNowTimestamp + 3600),
            new NotBefore($this->testNowTimestamp),
            new IssuedAt($this->testNowTimestamp),
            new JwtId('foobar'),
        ];

        $collection = Collection::make($claims);

        $payload = new Payload($collection);

        $this->storage->shouldReceive('get')->with('foobar')->once()->andReturn(['valid_until' => $this->testNowTimestamp]);

        $this->assertTrue($this->blacklist->has($payload));
    }

    public function blacklist_provider()
    {
        return [
            [null],
            [0],
            [''],
            [[]],
            [['valid_until' => strtotime('+1day')]],
        ];
    }

    /**
     * @test
     * @dataProvider blacklist_provider
     *
     * @param mixed $result
     */
    public function itShouldCheckWhetherATokenHasNotBeenBlacklisted($result)
    {
        $claims = [
            new Subject(1),
            new Issuer('http://example.com'),
            new Expiration($this->testNowTimestamp + 3600),
            new NotBefore($this->testNowTimestamp),
            new IssuedAt($this->testNowTimestamp),
            new JwtId('foobar'),
        ];

        $collection = Collection::make($claims);

        $payload = new Payload($collection);

        $this->storage->shouldReceive('get')->with('foobar')->once()->andReturn($result);
        $this->assertFalse($this->blacklist->has($payload));
    }

    /** @test */
    public function itShouldCheckWhetherATokenHasBeenBlacklistedForever()
    {
        $claims = [
            new Subject(1),
            new Issuer('http://example.com'),
            new Expiration($this->testNowTimestamp + 3600),
            new NotBefore($this->testNowTimestamp),
            new IssuedAt($this->testNowTimestamp),
            new JwtId('foobar'),
        ];
        $collection = Collection::make($claims);

        $payload = new Payload($collection);

        $this->storage->shouldReceive('get')->with('foobar')->once()->andReturn('forever');

        $this->assertTrue($this->blacklist->has($payload));
    }

    /** @test */
    public function itShouldCheckWhetherATokenHasBeenBlacklistedWhenTheTokenIsNotBlacklisted()
    {
        $claims = [
            new Subject(1),
            new Issuer('http://example.com'),
            new Expiration($this->testNowTimestamp + 3600),
            new NotBefore($this->testNowTimestamp),
            new IssuedAt($this->testNowTimestamp),
            new JwtId('foobar'),
        ];
        $collection = Collection::make($claims);

        $payload = new Payload($collection);

        $this->storage->shouldReceive('get')->with('foobar')->once()->andReturn(null);

        $this->assertFalse($this->blacklist->has($payload));
    }

    /** @test */
    public function itShouldRemoveATokenFromTheBlacklist()
    {
        $claims = [
            new Subject(1),
            new Issuer('http://example.com'),
            new Expiration($this->testNowTimestamp + 3600),
            new NotBefore($this->testNowTimestamp),
            new IssuedAt($this->testNowTimestamp),
            new JwtId('foobar'),
        ];
        $collection = Collection::make($claims);

        $payload = new Payload($collection);

        $this->storage->shouldReceive('destroy')->with('foobar')->andReturn(true);
        $this->assertTrue($this->blacklist->remove($payload));
    }

    /** @test */
    public function itShouldSetACustomUniqueKeyForTheBlacklist()
    {
        $claims = [
            new Subject(1),
            new Issuer('http://example.com'),
            new Expiration($this->testNowTimestamp + 3600),
            new NotBefore($this->testNowTimestamp),
            new IssuedAt($this->testNowTimestamp),
            new JwtId('foobar'),
        ];
        $collection = Collection::make($claims);

        $payload = new Payload($collection);

        $this->storage->shouldReceive('get')->with(1)->once()->andReturn(['valid_until' => $this->testNowTimestamp]);

        $this->assertTrue($this->blacklist->setKey('sub')->has($payload));
        $this->assertSame(1, $this->blacklist->getKey($payload));
    }

    /** @test */
    public function itShouldEmptyTheBlacklist()
    {
        $this->storage->shouldReceive('flush');
        $this->assertTrue($this->blacklist->clear());
    }

    /** @test */
    public function itShouldSetAndGetTheBlacklistGracePeriod()
    {
        $this->assertInstanceOf(Blacklist::class, $this->blacklist->setGracePeriod(15));
        $this->assertSame(15, $this->blacklist->getGracePeriod());
    }

    /** @test */
    public function itShouldSetAndGetTheBlacklistRefreshTtl()
    {
        $this->assertInstanceOf(Blacklist::class, $this->blacklist->setRefreshTtl(15));
        $this->assertSame(15, $this->blacklist->getRefreshTtl());
    }
}
