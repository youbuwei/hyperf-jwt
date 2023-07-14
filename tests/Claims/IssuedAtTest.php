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

use Youbuwei\HyperfJwt\Claims\IssuedAt;
use Youbuwei\HyperfJwt\Exceptions\InvalidClaimException;
use HyperfTest\AbstractTestCase;

/**
 * @internal
 * @coversNothing
 */
class IssuedAtTest extends AbstractTestCase
{
    /** @test */
    public function itShouldThrowAnExceptionWhenPassingAFutureTimestamp()
    {
        $this->expectExceptionMessage('Invalid value provided for claim [iat]');
        $this->expectException(InvalidClaimException::class);
        new IssuedAt($this->testNowTimestamp + 3600);
    }
}
