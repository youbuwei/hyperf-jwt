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

use Hyperf\Context\ApplicationContext;
use Youbuwei\HyperfJwt\Contracts\TokenValidatorInterface;

class Token
{
    /**
     * @var string
     */
    private $value;

    /**
     * @var \Youbuwei\HyperfJwt\Contracts\TokenValidatorInterface
     */
    private $validator;

    /**
     * Create a new JSON Web Token.
     */
    public function __construct(string $value)
    {
        $this->validator = ApplicationContext::getContainer()->get(TokenValidatorInterface::class);
        $this->value = (string) $this->validator->check($value);
    }

    /**
     * Get the token when casting to string.
     */
    public function __toString(): string
    {
        return $this->get();
    }

    /**
     * Get the token.
     */
    public function get(): string
    {
        return $this->value;
    }
}
