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

use Hyperf\Context\Context;
use Psr\Http\Message\ServerRequestInterface;
use Youbuwei\HyperfJwt\Contracts\JwtSubjectInterface;
use Youbuwei\HyperfJwt\Contracts\ManagerInterface;
use Youbuwei\HyperfJwt\Contracts\RequestParser\RequestParserInterface;
use Youbuwei\HyperfJwt\Exceptions\JwtException;

class Jwt
{
    use CustomClaims;

    /**
     * @var \Youbuwei\HyperfJwt\Manager
     */
    protected $manager;

    /**
     * @var \Youbuwei\HyperfJwt\Contracts\RequestParser\RequestParserInterface
     */
    protected $requestParser;

    /**
     * @var \Psr\Http\Message\ServerRequestInterface
     */
    protected $request;

    /**
     * Lock the subject.
     *
     * @var bool
     */
    protected $lockSubject = true;

    public function __construct(
        ManagerInterface $manager,
        RequestParserInterface $requestParser,
        ServerRequestInterface $request
    ) {
        $this->manager = $manager;
        $this->requestParser = $requestParser;
        $this->request = $request;
    }

    /**
     * Magically call the Jwt Manager.
     *
     * @return mixed
     * @throws \BadMethodCallException
     */
    public function __call(string $method, array $parameters)
    {
        if (method_exists($this->manager, $method)) {
            return call_user_func_array([$this->manager, $method], $parameters);
        }

        throw new \BadMethodCallException("Method [{$method}] does not exist.");
    }

    /**
     * Generate a token for a given subject.
     */
    public function fromSubject(JwtSubjectInterface $subject): string
    {
        $payload = $this->makePayload($subject);

        return $this->manager->encode($payload)->get();
    }

    /**
     * Alias to generate a token for a given user.
     */
    public function fromUser(JwtSubjectInterface $user): string
    {
        return $this->fromSubject($user);
    }

    /**
     * Refresh an expired token.
     *
     * @throws \Youbuwei\HyperfJwt\Exceptions\JwtException
     */
    public function refresh(bool $forceForever = false): string
    {
        $this->requireToken();

        $this->setToken(
            $token = $this->manager
                ->refresh($this->getToken(), $forceForever, array_merge(
                    $this->getCustomClaims(),
                    ($prv = $this->getPayload(true)->get('prv')) ? ['prv' => $prv] : []
                ))
                ->get()
        );

        return $token;
    }

    /**
     * Invalidate a token (add it to the blacklist).
     *
     * @return $this
     * @throws \Youbuwei\HyperfJwt\Exceptions\JwtException
     */
    public function invalidate(bool $forceForever = false)
    {
        $this->requireToken();

        $this->manager->invalidate($this->getToken(), $forceForever);

        return $this;
    }

    /**
     * Alias to get the payload, and as a result checks that
     * the token is valid i.e. not expired or blacklisted.
     *
     * @throws \Youbuwei\HyperfJwt\Exceptions\JwtException
     */
    public function checkOrFail(): Payload
    {
        return $this->getPayload();
    }

    /**
     * Check that the token is valid.
     *
     * @return bool|\Youbuwei\HyperfJwt\Payload
     */
    public function check(bool $getPayload = false)
    {
        try {
            $payload = $this->checkOrFail();
        } catch (JwtException $e) {
            return false;
        }

        return $getPayload ? $payload : true;
    }

    /**
     * Get the token.
     */
    public function getToken(): ?Token
    {
        if (empty($token = Context::get(Token::class))) {
            try {
                $this->parseToken();
                $token = Context::get(Token::class);
            } catch (JwtException $e) {
                $token = null;
            }
        }

        return $token;
    }

    /**
     * Parse the token from the request.
     *
     * @return $this
     * @throws \Youbuwei\HyperfJwt\Exceptions\JwtException
     */
    public function parseToken()
    {
        if (! $token = $this->getRequestParser()->parseToken($this->request)) {
            throw new JwtException('The token could not be parsed from the request');
        }

        return $this->setToken($token);
    }

    /**
     * Get the raw Payload instance.
     * @throws \Youbuwei\HyperfJwt\Exceptions\JwtException
     */
    public function getPayload(bool $ignoreExpired = false): Payload
    {
        $this->requireToken();

        return $this->manager->decode($this->getToken(), true, $ignoreExpired);
    }

    /**
     * Convenience method to get a claim value.
     *
     * @return mixed
     * @throws \Youbuwei\HyperfJwt\Exceptions\JwtException
     */
    public function getClaim(string $claim)
    {
        return $this->getPayload()->get($claim);
    }

    /**
     * Create a Payload instance.
     */
    public function makePayload(JwtSubjectInterface $subject): Payload
    {
        return $this->getPayloadFactory()->make($this->getClaimsArray($subject));
    }

    /**
     * Check if the subject model matches the one saved in the token.
     *
     * @param object|string $model
     *
     * @throws \Youbuwei\HyperfJwt\Exceptions\JwtException
     */
    public function checkSubjectModel($model): bool
    {
        if (($prv = $this->getPayload()->get('prv')) === null) {
            return true;
        }

        return $this->hashSubjectModel($model) === $prv;
    }

    /**
     * Set the token.
     *
     * @param string|\Youbuwei\HyperfJwt\Token $token
     *
     * @return $this
     */
    public function setToken($token)
    {
        Context::set(Token::class, $token instanceof Token ? $token : new Token($token));

        return $this;
    }

    /**
     * Unset the current token.
     *
     * @return $this
     */
    public function unsetToken()
    {
        Context::destroy(Token::class);

        return $this;
    }

    /**
     * @return $this
     */
    public function setRequest(ServerRequestInterface $request)
    {
        $this->request = $request;

        return $this;
    }

    /**
     * Set whether the subject should be "locked".
     *
     * @return $this
     */
    public function setLockSubject(bool $lock)
    {
        $this->lockSubject = $lock;

        return $this;
    }

    /**
     * Get the Manager instance.
     */
    public function getManager(): Manager
    {
        return $this->manager;
    }

    /**
     * Get the Parser instance.
     */
    public function getRequestParser(): RequestParserInterface
    {
        return $this->requestParser;
    }

    /**
     * Get the Payload Factory.
     */
    public function getPayloadFactory(): PayloadFactory
    {
        return $this->manager->getPayloadFactory();
    }

    /**
     * Get the Blacklist.
     */
    public function getBlacklist(): Blacklist
    {
        return $this->manager->getBlacklist();
    }

    /**
     * Build the claims array and return it.
     */
    protected function getClaimsArray(JwtSubjectInterface $subject): array
    {
        return array_merge(
            $this->getClaimsForSubject($subject),
            $subject->getJwtCustomClaims(), // custom claims from JwtSubject method
            $this->customClaims // custom claims from inline setter
        );
    }

    /**
     * Get the claims associated with a given subject.
     */
    protected function getClaimsForSubject(JwtSubjectInterface $subject): array
    {
        return array_merge([
            'sub' => $subject->getJwtIdentifier(),
        ], $this->lockSubject ? ['prv' => $this->hashSubjectModel($subject)] : []);
    }

    /**
     * Hash the subject model and return it.
     *
     * @param object|string $model
     */
    protected function hashSubjectModel($model): string
    {
        return sha1(is_object($model) ? get_class($model) : (string) $model);
    }

    /**
     * Ensure that a token is available.
     *
     * @throws \Youbuwei\HyperfJwt\Exceptions\JwtException
     */
    protected function requireToken()
    {
        if (! $this->getToken()) {
            throw new JwtException('A token is required');
        }
    }
}
