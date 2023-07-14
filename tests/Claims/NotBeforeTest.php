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

use Youbuwei\HyperfJwt\Claims\NotBefore;
use Youbuwei\HyperfJwt\Exceptions\InvalidClaimException;
use HyperfTest\AbstractTestCase;

/**
 * @internal
 * @coversNothing
 */
class NotBeforeTest extends AbstractTestCase
{
    /**
     * @test
     */
    public function itShouldThrowAnExceptionWhenPassingAnInvalidValue()
    {
        $this->expectExceptionMessage('Invalid value provided for claim [nbf]');
        $this->expectException(InvalidClaimException::class);
        new NotBefore('foo');
    }
}
