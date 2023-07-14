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

use Youbuwei\HyperfJwt\Token;

/**
 * @internal
 * @coversNothing
 */
class TokenTest extends AbstractTestCase
{
    /**
     * @var \Youbuwei\HyperfJwt\Token
     */
    protected $token;

    public function setUp(): void
    {
        parent::setUp();

        $this->token = new Token('foo.bar.baz');
    }

    /** @test */
    public function itShouldReturnTheTokenWhenCastingToAString()
    {
        $this->assertEquals((string) $this->token, $this->token);
    }

    /** @test */
    public function itShouldReturnTheTokenWhenCallingGetMethod()
    {
        $this->assertIsString($this->token->get());
    }
}
