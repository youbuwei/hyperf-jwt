<?php

declare(strict_types=1);
/**
 * This file is part of youbuwei/hyperf-jwt
 *
 * @link     https://github.com/youbuwei/hyperf-jwt
 * @contact  youbuwei@yahoo.com
 * @license  https://github.com/youbuwei/hyperf-jwt/blob/master/LICENSE
 */
namespace Youbuwei\HyperfJwt\Validators;

use Hyperf\Contract\ConfigInterface;
use Youbuwei\HyperfJwt\Claims\Collection;
use Youbuwei\HyperfJwt\Contracts\PayloadValidatorInterface;
use Youbuwei\HyperfJwt\Exceptions\JwtException;
use Youbuwei\HyperfJwt\Exceptions\TokenInvalidException;

class PayloadValidator implements PayloadValidatorInterface
{
    /**
     * The required claims.
     *
     * @var array
     */
    protected $requiredClaims = [];

    public function __construct(ConfigInterface $config)
    {
        $this->setRequiredClaims($config->get('jwt.required_claims', []));
    }

    public function check(Collection $value, bool $ignoreExpired = false): Collection
    {
        $this->validateStructure($value);

        return $this->validatePayload($value, $ignoreExpired);
    }

    public function isValid(Collection $value, bool $ignoreExpired = false): bool
    {
        try {
            $this->check($value, $ignoreExpired);
        } catch (JwtException $e) {
            return false;
        }

        return true;
    }

    /**
     * Set the required claims.
     *
     * @return $this
     */
    public function setRequiredClaims(array $claims)
    {
        $this->requiredClaims = $claims;

        return $this;
    }

    /**
     * Ensure the payload contains the required claims and
     * the claims have the relevant type.
     *
     * @throws \Youbuwei\HyperfJwt\Exceptions\TokenInvalidException
     */
    protected function validateStructure(Collection $claims)
    {
        if ($this->requiredClaims and ! $claims->hasAllClaims($this->requiredClaims)) {
            throw new TokenInvalidException('JWT payload does not contain the required claims');
        }
        return $this;
    }

    /**
     * Validate the payload timestamps.
     *
     * @throws \Youbuwei\HyperfJwt\Exceptions\TokenExpiredException
     * @throws \Youbuwei\HyperfJwt\Exceptions\TokenInvalidException
     */
    protected function validatePayload(Collection $claims, bool $ignoreExpired = false): Collection
    {
        return $claims->validate($ignoreExpired);
    }
}
