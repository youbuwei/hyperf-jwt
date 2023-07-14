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

use FastRoute\Dispatcher;
use Hyperf\Context\Context;
use Hyperf\HttpMessage\Server\Request;
use Hyperf\HttpServer\Request as HttpServerRequest;
use Hyperf\HttpServer\Router\Dispatched;
use Psr\Http\Message\ServerRequestInterface;
use Youbuwei\HyperfJwt\RequestParser\Handlers\AuthHeaders;
use Youbuwei\HyperfJwt\RequestParser\Handlers\Cookies;
use Youbuwei\HyperfJwt\RequestParser\Handlers\InputSource;
use Youbuwei\HyperfJwt\RequestParser\Handlers\QueryString;
use Youbuwei\HyperfJwt\RequestParser\Handlers\RouteParams;
use Youbuwei\HyperfJwt\RequestParser\RequestParser;

/**
 * @internal
 * @coversNothing
 */
class RequestParserTest extends AbstractTestCase
{
    /** @test */
    public function itShouldReturnTheTokenFromTheAuthorizationHeader()
    {
        Context::set(ServerRequestInterface::class, new Request('POST', 'foo', [
            'Authorization' => 'Bearer foobar',
        ]));
        $request = new HttpServerRequest();

        $parser = new RequestParser();

        $parser->setHandlers([
            new QueryString(),
            new InputSource(),
            new AuthHeaders(),
            new RouteParams(),
        ]);

        $this->assertSame($parser->parseToken($request), 'foobar');
        $this->assertTrue($parser->hasToken($request));
    }

    /** @test */
    public function itShouldReturnTheTokenFromThePrefixedAuthenticationHeader()
    {
        Context::set(ServerRequestInterface::class, new Request('POST', 'foo', [
            'Authorization' => 'Custom foobar',
        ]));
        $request = new HttpServerRequest();

        $parser = new RequestParser();

        $parser->setHandlers([
            new QueryString(),
            new InputSource(),
            (new AuthHeaders())->setHeaderPrefix('Custom'),
            new RouteParams(),
        ]);

        $this->assertSame($parser->parseToken($request), 'foobar');
        $this->assertTrue($parser->hasToken($request));
    }

    /** @test */
    public function itShouldReturnTheTokenFromTheCustomAuthenticationHeader()
    {
        Context::set(ServerRequestInterface::class, new Request('POST', 'foo', [
            'custom_authorization' => 'Bearer foobar',
        ]));
        $request = new HttpServerRequest();

        $parser = new RequestParser();

        $parser->setHandlers([
            new QueryString(),
            new InputSource(),
            (new AuthHeaders())->setHeaderName('custom_authorization'),
            new RouteParams(),
        ]);

        $this->assertSame($parser->parseToken($request), 'foobar');
        $this->assertTrue($parser->hasToken($request));
    }

    /** @test */
    public function itShouldReturnTheTokenFromQueryString()
    {
        Context::set(
            ServerRequestInterface::class,
            (new Request('GET', '/'))
                ->withAttribute(Dispatched::class, new Dispatched([
                    Dispatcher::FOUND, null, ['token' => 'foobar'],
                ]))
        );
        $request = new HttpServerRequest();

        $parser = new RequestParser();
        $parser->setHandlers([
            new AuthHeaders(),
            new QueryString(),
            new InputSource(),
            new RouteParams(),
        ]);

        $this->assertSame($parser->parseToken($request), 'foobar');
        $this->assertTrue($parser->hasToken($request));
    }

    /** @test */
    public function itShouldReturnTheTokenFromTheCustomQueryString()
    {
        Context::set(
            ServerRequestInterface::class,
            (new Request('GET', '/foo'))
                ->withQueryParams(['custom_token_key' => 'foobar'])
                ->withAttribute(Dispatched::class, new Dispatched([
                    Dispatcher::FOUND, null, ['custom_token_key' => 'foobar'],
                ]))
        );
        $request = new HttpServerRequest();

        $parser = new RequestParser();
        $parser->setHandlers([
            new AuthHeaders(),
            (new QueryString())->setKey('custom_token_key'),
            new InputSource(),
            new RouteParams(),
        ]);

        $this->assertSame($parser->parseToken($request), 'foobar');
        $this->assertTrue($parser->hasToken($request));
    }

    /** @test */
    public function itShouldReturnTheTokenFromTheQueryStringNotTheInputSource()
    {
        Context::set(
            ServerRequestInterface::class,
            (new Request('POST', 'foo'))
                ->withQueryParams(['token' => 'foobar'])
                ->withParsedBody(['token' => 'foobarbaz'])
        );
        $request = new HttpServerRequest();

        $parser = new RequestParser();
        $parser->setHandlers([
            new AuthHeaders(),
            new QueryString(),
            new InputSource(),
            new RouteParams(),
        ]);

        $this->assertSame($parser->parseToken($request), 'foobar');
        $this->assertTrue($parser->hasToken($request));
    }

    /** @test */
    public function itShouldReturnTheTokenFromTheCustomQueryStringNotTheCustomInputSource()
    {
        Context::set(
            ServerRequestInterface::class,
            (new Request('POST', 'foo'))
                ->withQueryParams(['custom_token_key' => 'foobar'])
                ->withParsedBody(['custom_token_key' => 'foobarbaz'])
        );
        $request = new HttpServerRequest();

        $parser = new RequestParser();
        $parser->setHandlers([
            new AuthHeaders(),
            (new QueryString())->setKey('custom_token_key'),
            (new InputSource())->setKey('custom_token_key'),
            new RouteParams(),
        ]);

        $this->assertSame($parser->parseToken($request), 'foobar');
        $this->assertTrue($parser->hasToken($request));
    }

    /** @test */
    public function itShouldReturnTheTokenFromInputSource()
    {
        Context::set(
            ServerRequestInterface::class,
            (new Request('POST', 'foo'))
                ->withParsedBody(['token' => 'foobar'])
        );
        $request = new HttpServerRequest();

        $parser = new RequestParser();
        $parser->setHandlers([
            new AuthHeaders(),
            new QueryString(),
            new InputSource(),
            new RouteParams(),
        ]);

        $this->assertSame($parser->parseToken($request), 'foobar');
        $this->assertTrue($parser->hasToken($request));
    }

    /** @test */
    public function itShouldReturnTheTokenFromTheCustomInputSource()
    {
        Context::set(
            ServerRequestInterface::class,
            (new Request('POST', 'foo'))
                ->withParsedBody(['custom_token_key' => 'foobar'])
        );
        $request = new HttpServerRequest();

        $parser = new RequestParser();
        $parser->setHandlers([
            new AuthHeaders(),
            new QueryString(),
            (new InputSource())->setKey('custom_token_key'),
            new RouteParams(),
        ]);

        $this->assertSame($parser->parseToken($request), 'foobar');
        $this->assertTrue($parser->hasToken($request));
    }

    /** @test */
    public function itShouldReturnTheTokenFromAnUnencryptedCookie()
    {
        Context::set(ServerRequestInterface::class, (new Request('POST', 'foo'))->withCookieParams([
            'token' => 'foobar',
        ]));
        $request = new HttpServerRequest();

        $parser = new RequestParser();
        $parser->setHandlers([
            new AuthHeaders(),
            new QueryString(),
            new InputSource(),
            new RouteParams(),
            new Cookies(),
        ]);

        $this->assertSame($parser->parseToken($request), 'foobar');
        $this->assertTrue($parser->hasToken($request));
    }

    /** @test */
    public function itShouldReturnTheTokenFromRoute()
    {
        Context::set(ServerRequestInterface::class, (new Request('GET', 'foo'))->withAttribute(Dispatched::class, new Dispatched([
            Dispatcher::FOUND, null, ['token' => 'foobar'],
        ])));
        $request = new HttpServerRequest();

        $parser = new RequestParser();
        $parser->setHandlers([
            new AuthHeaders(),
            new QueryString(),
            new InputSource(),
            new RouteParams(),
        ]);

        $this->assertSame($parser->parseToken($request), 'foobar');
        $this->assertTrue($parser->hasToken($request));
    }

    /** @test */
    public function itShouldReturnTheTokenFromRouteWithACustomParam()
    {
        Context::set(ServerRequestInterface::class, (new Request('GET', 'foo'))->withAttribute(Dispatched::class, new Dispatched([
            Dispatcher::FOUND, null, ['custom_route_param' => 'foobar'],
        ])));
        $request = new HttpServerRequest();

        $parser = new RequestParser();
        $parser->setHandlers([
            new AuthHeaders(),
            new QueryString(),
            new InputSource(),
            (new RouteParams())->setKey('custom_route_param'),
        ]);

        $this->assertSame($parser->parseToken($request), 'foobar');
        $this->assertTrue($parser->hasToken($request));
    }

    /** @test */
    public function itShouldIgnoreRoutelessRequests()
    {
        Context::set(ServerRequestInterface::class, (new Request('GET', 'foo'))->withAttribute(Dispatched::class, new Dispatched([
            Dispatcher::FOUND, null, [],
        ])));
        $request = new HttpServerRequest();

        $parser = new RequestParser();
        $parser->setHandlers([
            new AuthHeaders(),
            new QueryString(),
            new InputSource(),
            new RouteParams(),
        ]);

        $this->assertNull($parser->parseToken($request));
        $this->assertFalse($parser->hasToken($request));
    }

    /** @test */
    public function itShouldReturnNullIfNoTokenInRequest()
    {
        Context::set(ServerRequestInterface::class, (new Request('GET', 'foo'))->withAttribute(Dispatched::class, new Dispatched([
            Dispatcher::FOUND, null, [],
        ])));
        $request = new HttpServerRequest();

        $parser = new RequestParser();
        $parser->setHandlers([
            new AuthHeaders(),
            new QueryString(),
            new InputSource(),
            new RouteParams(),
        ]);

        $this->assertNull($parser->parseToken($request));
        $this->assertFalse($parser->hasToken($request));
    }

    /** @test */
    public function itShouldRetrieveTheHandlers()
    {
        $handlers = [
            new AuthHeaders(),
            new QueryString(),
            new InputSource(),
            new RouteParams(),
        ];

        $parser = new RequestParser();
        $parser->setHandlers($handlers);

        $this->assertSame($parser->getHandlers(), $handlers);
    }

    /** @test */
    public function itShouldSetTheCookieKey()
    {
        $cookies = (new Cookies())->setKey('test');
        $this->assertInstanceOf(Cookies::class, $cookies);
    }
}
