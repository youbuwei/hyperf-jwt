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

use Carbon\Carbon;
use HyperfTest\AbstractTestCase;
use Youbuwei\HyperfJwt\Claims\Collection;
use Youbuwei\HyperfJwt\Claims\Expiration;
use Youbuwei\HyperfJwt\Claims\IssuedAt;
use Youbuwei\HyperfJwt\Claims\Issuer;
use Youbuwei\HyperfJwt\Claims\JwtId;
use Youbuwei\HyperfJwt\Claims\NotBefore;
use Youbuwei\HyperfJwt\Claims\Subject;
use Youbuwei\HyperfJwt\Payload;

/**
 * @internal
 * @coversNothing
 */
class DatetimeClaimTest extends AbstractTestCase
{
    /**
     * @var array
     */
    protected $claimsTimestamp;

    public function setUp(): void
    {
        parent::setUp();

        $this->claimsTimestamp = [
            'sub' => new Subject(1),
            'iss' => new Issuer('http://example.com'),
            'exp' => new Expiration($this->testNowTimestamp + 3600),
            'nbf' => new NotBefore($this->testNowTimestamp),
            'iat' => new IssuedAt($this->testNowTimestamp),
            'jti' => new JwtId('foo'),
        ];
    }

    /** @test */
    public function itShouldHandleCarbonClaims()
    {
        $testCarbon = Carbon::createFromTimestampUTC($this->testNowTimestamp);
        $testCarbonCopy = clone $testCarbon;

        $this->assertInstanceOf(Carbon::class, $testCarbon);
        $this->assertInstanceOf(\DateTime::class, $testCarbon);
        $this->assertInstanceOf(\DatetimeInterface::class, $testCarbon);

        $claimsDatetime = [
            'sub' => new Subject(1),
            'iss' => new Issuer('http://example.com'),
            'exp' => new Expiration($testCarbonCopy->addHour()),
            'nbf' => new NotBefore($testCarbon),
            'iat' => new IssuedAt($testCarbon),
            'jti' => new JwtId('foo'),
        ];

        $payloadTimestamp = new Payload(Collection::make($this->claimsTimestamp));
        $payloadDatetime = new Payload(Collection::make($claimsDatetime));

        $this->assertEquals($payloadTimestamp, $payloadDatetime);
    }

    /** @test */
    public function itShouldHandleDatetimeClaims()
    {
        $testDateTime = \DateTime::createFromFormat('U', (string) $this->testNowTimestamp);
        $testDateTimeCopy = clone $testDateTime;

        $this->assertInstanceOf(\DateTime::class, $testDateTime);
        $this->assertInstanceOf(\DatetimeInterface::class, $testDateTime);

        $claimsDatetime = [
            'sub' => new Subject(1),
            'iss' => new Issuer('http://example.com'),
            'exp' => new Expiration($testDateTimeCopy->modify('+3600 seconds')),
            'nbf' => new NotBefore($testDateTime),
            'iat' => new IssuedAt($testDateTime),
            'jti' => new JwtId('foo'),
        ];

        $payloadTimestamp = new Payload(Collection::make($this->claimsTimestamp));
        $payloadDatetime = new Payload(Collection::make($claimsDatetime));

        $this->assertEquals($payloadTimestamp, $payloadDatetime);
    }

    /** @test */
    public function itShouldHandleDatetimeImmutableClaims()
    {
        $testDateTimeImmutable = \DateTimeImmutable::createFromFormat('U', (string) $this->testNowTimestamp);

        $this->assertInstanceOf(\DateTimeImmutable::class, $testDateTimeImmutable);
        $this->assertInstanceOf(\DatetimeInterface::class, $testDateTimeImmutable);

        $claimsDatetime = [
            'sub' => new Subject(1),
            'iss' => new Issuer('http://example.com'),
            'exp' => new Expiration($testDateTimeImmutable->modify('+3600 seconds')),
            'nbf' => new NotBefore($testDateTimeImmutable),
            'iat' => new IssuedAt($testDateTimeImmutable),
            'jti' => new JwtId('foo'),
        ];

        $payloadTimestamp = new Payload(Collection::make($this->claimsTimestamp));
        $payloadDatetime = new Payload(Collection::make($claimsDatetime));

        $this->assertEquals($payloadTimestamp, $payloadDatetime);
    }

    /** @test */
    public function itShouldHandleDatetintervalClaims()
    {
        $testDateInterval = new \DateInterval('PT1H');

        $this->assertInstanceOf(\DateInterval::class, $testDateInterval);

        $claimsDateInterval = [
            'sub' => new Subject(1),
            'iss' => new Issuer('http://example.com'),
            'exp' => new Expiration($testDateInterval),
            'nbf' => new NotBefore($this->testNowTimestamp),
            'iat' => new IssuedAt($this->testNowTimestamp),
            'jti' => new JwtId('foo'),
        ];

        $payloadTimestamp = new Payload(Collection::make($this->claimsTimestamp));
        $payloadDateInterval = new Payload(Collection::make($claimsDateInterval));

        $this->assertEquals($payloadTimestamp, $payloadDateInterval);
    }
}
