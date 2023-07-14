<?php

declare(strict_types=1);
/**
 * This file is part of youbuwei/hyperf-jwt
 *
 * @link     https://github.com/youbuwei/hyperf-jwt
 * @contact  youbuwei@yahoo.com
 * @license  https://github.com/youbuwei/hyperf-jwt/blob/master/LICENSE
 */
namespace HyperfTest\Claims;

use Hyperf\HttpMessage\Server\Request;
use Youbuwei\HyperfJwt\Claims\Custom;
use Youbuwei\HyperfJwt\Claims\Expiration;
use Youbuwei\HyperfJwt\Claims\Factory;
use Youbuwei\HyperfJwt\Claims\IssuedAt;
use Youbuwei\HyperfJwt\Claims\Issuer;
use Youbuwei\HyperfJwt\Claims\JwtId;
use Youbuwei\HyperfJwt\Claims\NotBefore;
use Youbuwei\HyperfJwt\Claims\Subject;
use HyperfTest\AbstractTestCase;
use HyperfTest\Fixtures\Foo;
use Psr\Http\Message\ServerRequestInterface;

/**
 * @internal
 * @coversNothing
 */
class FactoryTest extends AbstractTestCase
{
    /**
     * @var \Youbuwei\HyperfJwt\Claims\Factory
     */
    protected $factory;

    public function setUp(): void
    {
        parent::setUp();

        $this->factory = new Factory(3600, 3600 * 24 * 14);
        $this->container->set(ServerRequestInterface::class, new Request('GET', 'http://localhost/foo'));
    }

    /** @test */
    public function itShouldSetTheTtl()
    {
        $this->assertInstanceOf(Factory::class, $this->factory->setTtl(30));
    }

    /** @test */
    public function itShouldGetTheTtl()
    {
        $this->factory->setTtl($ttl = 30);
        $this->assertSame($ttl, $this->factory->getTtl());
    }

    /** @test */
    public function itShouldGetADefinedClaimInstanceWhenPassingANameAndValue()
    {
        $this->assertInstanceOf(Subject::class, $this->factory->get('sub', 1));
        $this->assertInstanceOf(Issuer::class, $this->factory->get('iss', 'http://example.com'));
        $this->assertInstanceOf(Expiration::class, $this->factory->get('exp', $this->testNowTimestamp + 3600));
        $this->assertInstanceOf(NotBefore::class, $this->factory->get('nbf', $this->testNowTimestamp));
        $this->assertInstanceOf(IssuedAt::class, $this->factory->get('iat', $this->testNowTimestamp));
        $this->assertInstanceOf(JwtId::class, $this->factory->get('jti', 'foo'));
    }

    /** @test */
    public function itShouldGetACustomClaimInstanceWhenPassingANonDefinedNameAndValue()
    {
        $this->assertInstanceOf(Custom::class, $this->factory->get('foo', ['bar']));
    }

    /** @test */
    public function itShouldMakeAClaimInstanceWithAValue()
    {
        $iat = $this->factory->make('iat');
        $this->assertSame($iat->getValue(), $this->testNowTimestamp);
        $this->assertInstanceOf(IssuedAt::class, $iat);

        $nbf = $this->factory->make('nbf');
        $this->assertSame($nbf->getValue(), $this->testNowTimestamp);
        $this->assertInstanceOf(NotBefore::class, $nbf);

        $iss = $this->factory->make('iss');
        $this->assertSame($iss->getValue(), 'http://localhost/foo');
        $this->assertInstanceOf(Issuer::class, $iss);

        $exp = $this->factory->make('exp');
        $this->assertSame($exp->getValue(), $this->testNowTimestamp + 3600);
        $this->assertInstanceOf(Expiration::class, $exp);

        $jti = $this->factory->make('jti');
        $this->assertInstanceOf(JwtId::class, $jti);
    }

    /** @test */
    public function itShouldExtendClaimFactoryToAddACustomClaim()
    {
        $this->factory->extend('foo', Foo::class);

        $this->assertInstanceOf(Foo::class, $this->factory->get('foo', 'bar'));
    }
}
