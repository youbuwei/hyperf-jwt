<?php

declare(strict_types=1);
/**
 * This file is part of youbuwei/hyperf-jwt
 *
 * @link     https://github.com/youbuwei/hyperf-jwt
 * @contact  youbuwei@yahoo.com
 * @license  https://github.com/youbuwei/hyperf-jwt/blob/master/LICENSE
 */
namespace HyperfTest\Validators;

use Youbuwei\HyperfJwt\Claims\Collection;
use Youbuwei\HyperfJwt\Claims\Expiration;
use Youbuwei\HyperfJwt\Claims\IssuedAt;
use Youbuwei\HyperfJwt\Claims\Issuer;
use Youbuwei\HyperfJwt\Claims\JwtId;
use Youbuwei\HyperfJwt\Claims\NotBefore;
use Youbuwei\HyperfJwt\Claims\Subject;
use Youbuwei\HyperfJwt\Contracts\PayloadValidatorInterface;
use Youbuwei\HyperfJwt\Exceptions\InvalidClaimException;
use Youbuwei\HyperfJwt\Exceptions\TokenExpiredException;
use Youbuwei\HyperfJwt\Exceptions\TokenInvalidException;
use HyperfTest\AbstractTestCase;

/**
 * @internal
 * @coversNothing
 */
class PayloadValidatorTest extends AbstractTestCase
{
    /**
     * @var \Youbuwei\HyperfJwt\Validators\PayloadValidator
     */
    protected $validator;

    public function setUp(): void
    {
        parent::setUp();

        $this->validator = $this->container->get(PayloadValidatorInterface::class);
        $this->validator->setRequiredClaims([
            'iss',
            'iat',
            'exp',
            'nbf',
            'sub',
            'jti',
        ]);
    }

    /** @test */
    public function itShouldReturnTrueWhenProvidingAValidPayload()
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

        $this->assertTrue($this->validator->isValid($collection));
    }

    /** @test */
    public function itShouldThrowAnExceptionWhenProvidingAnExpiredPayload()
    {
        $this->expectExceptionMessage('Token has expired');
        $this->expectException(TokenExpiredException::class);
        $claims = [
            new Subject(1),
            new Issuer('http://example.com'),
            new Expiration($this->testNowTimestamp - 1440),
            new NotBefore($this->testNowTimestamp - 3660),
            new IssuedAt($this->testNowTimestamp - 3660),
            new JwtId('foo'),
        ];

        $collection = Collection::make($claims);

        $this->validator->check($collection);
    }

    /** @test */
    public function itShouldThrowAnExceptionWhenProvidingAnInvalidNbfClaim()
    {
        $this->expectExceptionMessage('Not Before (nbf) timestamp cannot be in the future');
        $this->expectException(TokenInvalidException::class);
        $claims = [
            new Subject(1),
            new Issuer('http://example.com'),
            new Expiration($this->testNowTimestamp + 1440),
            new NotBefore($this->testNowTimestamp + 3660),
            new IssuedAt($this->testNowTimestamp - 3660),
            new JwtId('foo'),
        ];

        $collection = Collection::make($claims);

        $this->validator->check($collection);
    }

    /** @test */
    public function itShouldThrowAnExceptionWhenProvidingAnInvalidIatClaim()
    {
        $this->expectExceptionMessage('Invalid value provided for claim [iat]');
        $this->expectException(InvalidClaimException::class);
        $claims = [
            new Subject(1),
            new Issuer('http://example.com'),
            new Expiration($this->testNowTimestamp + 1440),
            new NotBefore($this->testNowTimestamp - 3660),
            new IssuedAt($this->testNowTimestamp + 3660),
            new JwtId('foo'),
        ];

        $collection = Collection::make($claims);

        $this->validator->check($collection);
    }

    /** @test */
    public function itShouldThrowAnExceptionWhenProvidingAnInvalidPayload()
    {
        $this->expectExceptionMessage('JWT payload does not contain the required claims');
        $this->expectException(TokenInvalidException::class);
        $claims = [
            new Subject(1),
            new Issuer('http://example.com'),
        ];

        $collection = Collection::make($claims);

        $this->validator->check($collection);
    }

    /** @test */
    public function itShouldThrowAnExceptionWhenProvidingAnInvalidExpiry()
    {
        $this->expectExceptionMessage('Invalid value provided for claim [exp]');
        $this->expectException(InvalidClaimException::class);
        $claims = [
            new Subject(1),
            new Issuer('http://example.com'),
            new Expiration('foo'),
            new NotBefore($this->testNowTimestamp - 3660),
            new IssuedAt($this->testNowTimestamp + 3660),
            new JwtId('foo'),
        ];

        $collection = Collection::make($claims);

        $this->validator->check($collection);
    }

    /** @test */
    public function itShouldSetTheRequiredClaims()
    {
        $claims = [
            new Subject(1),
            new Issuer('http://example.com'),
        ];

        $collection = Collection::make($claims);

        $this->assertTrue($this->validator->setRequiredClaims(['iss', 'sub'])->isValid($collection));
    }

    /** @test */
    public function itShouldCheckTheTokenInTheRefreshContext()
    {
        $this->claimFactory->setRefreshTtl(3600);

        $claims = [
            new Subject(1),
            new Issuer('http://example.com'),
            new Expiration($this->testNowTimestamp - 1000),
            new NotBefore($this->testNowTimestamp),
            new IssuedAt($this->testNowTimestamp - 2600), // this is LESS than the refresh ttl at 1 hour
            new JwtId('foo'),
        ];

        $collection = Collection::make($claims);

        $this->assertTrue(
            $this->validator->isValid($collection, true)
        );
    }

    /** @test */
    public function itShouldReturnTrueIfTheRefreshTtlIsNull()
    {
        $this->claimFactory->setRefreshTtl(null);

        $claims = [
            new Subject(1),
            new Issuer('http://example.com'),
            new Expiration($this->testNowTimestamp - 1000),
            new NotBefore($this->testNowTimestamp),
            new IssuedAt($this->testNowTimestamp - 2600), // this is LESS than the refresh ttl at 1 hour
            new JwtId('foo'),
        ];

        $collection = Collection::make($claims);

        $this->assertTrue(
            $this->validator->isValid($collection, true)
        );
    }

    /** @test */
    public function itShouldThrowAnExceptionIfTheTokenCannotBeRefreshed()
    {
        $this->expectExceptionMessage('Token has expired and can no longer be refreshed');
        $this->expectException(TokenExpiredException::class);
        $this->claimFactory->setRefreshTtl(3600);

        $claims = [
            new Subject(1),
            new Issuer('http://example.com'),
            new Expiration($this->testNowTimestamp),
            new NotBefore($this->testNowTimestamp),
            new IssuedAt($this->testNowTimestamp - 5000), // this is MORE than the refresh ttl at 1 hour, so is invalid
            new JwtId('foo'),
        ];

        $collection = Collection::make($claims);

        $this->validator->check($collection, true);
    }
}
