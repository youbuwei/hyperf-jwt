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

use Hyperf\Utils\Contracts\Arrayable;
use Youbuwei\HyperfJwt\Claims\Expiration;
use HyperfTest\AbstractTestCase;

/**
 * @internal
 * @coversNothing
 */
class ClaimTest extends AbstractTestCase
{
    /**
     * @var \Youbuwei\HyperfJwt\Claims\Expiration
     */
    protected $claim;

    public function setUp(): void
    {
        parent::setUp();

        $this->claim = new Expiration($this->testNowTimestamp);
    }

    /** @test */
    public function itShouldThrowAnExceptionWhenPassingAnInvalidValue()
    {
        $this->expectExceptionMessage('Invalid value provided for claim [exp]');
        $this->expectException(\Youbuwei\HyperfJwt\Exceptions\InvalidClaimException::class);
        $this->claim->setValue('foo');
    }

    /** @test */
    public function itShouldConvertTheClaimToAnArray()
    {
        $this->assertSame(['exp' => $this->testNowTimestamp], $this->claim->toArray());
    }

    /** @test */
    public function itShouldGetTheClaimAsAString()
    {
        $this->assertJsonStringEqualsJsonString((string) $this->claim, $this->claim->toJson());
    }

    /** @test */
    public function itShouldGetTheObjectAsJson()
    {
        $this->assertJsonStringEqualsJsonString(json_encode($this->claim), $this->claim->toJson());
    }

    /** @test */
    public function itShouldImplementArrayable()
    {
        $this->assertInstanceOf(Arrayable::class, $this->claim);
    }
}
