<?php

declare(strict_types=1);
/**
 * This file is part of youbuwei/hyperf-jwt
 *
 * @link     https://github.com/youbuwei/hyperf-jwt
 * @contact  youbuwei@yahoo.com
 * @license  https://github.com/youbuwei/hyperf-jwt/blob/master/LICENSE
 */
namespace Youbuwei\HyperfJwt\Exceptions;

use Youbuwei\HyperfJwt\Contracts\ClaimInterface;

class InvalidClaimException extends JwtException
{
    /**
     * Constructor.
     *
     * @param int $code
     */
    public function __construct(ClaimInterface $claim, $code = 0, \Exception $previous = null)
    {
        parent::__construct('Invalid value provided for claim [' . $claim->getName() . ']', $code, $previous);
    }
}
