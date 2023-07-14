<?php

declare(strict_types=1);
/**
 * This file is part of youbuwei/hyperf-jwt
 *
 * @link     https://github.com/youbuwei/hyperf-jwt
 * @contact  youbuwei@yahoo.com
 * @license  https://github.com/youbuwei/hyperf-jwt/blob/master/LICENSE
 */
use Hyperf\Config\Config;
use Hyperf\Context\ApplicationContext;
use Hyperf\Context\Context;
use Hyperf\Contract\ConfigInterface;
use Hyperf\Di\Annotation\AnnotationReader;
use Hyperf\Di\ClassLoader;
use Hyperf\Di\Container;
use Hyperf\Di\Definition\DefinitionSourceFactory;
use Hyperf\HttpMessage\Server\Request;
use Hyperf\HttpServer\Request as HttpServerRequest;
use Psr\Http\Message\ServerRequestInterface;
use Youbuwei\HyperfJwt\Contracts\PayloadValidatorInterface;
use Youbuwei\HyperfJwt\Contracts\TokenValidatorInterface;
use Youbuwei\HyperfJwt\Validators\PayloadValidator;
use Youbuwei\HyperfJwt\Validators\TokenValidator;

! defined('BASE_PATH') && define('BASE_PATH', dirname(__DIR__, 1));
! defined('SWOOLE_HOOK_FLAGS') && define('SWOOLE_HOOK_FLAGS', SWOOLE_HOOK_ALL);

Swoole\Runtime::enableCoroutine(true);

require_once dirname(dirname(__FILE__)) . '/vendor/autoload.php';

//AnnotationReader::addGlobalIgnoredName('mixin');

ClassLoader::init();

$container = new Container((new DefinitionSourceFactory(true))());
$container->set(ConfigInterface::class, $config = new Config([]));

$container->set(PayloadValidatorInterface::class, new PayloadValidator($config));
$container->set(TokenValidatorInterface::class, new TokenValidator());
$container->set(ServerRequestInterface::class, new HttpServerRequest());
Context::set(ServerRequestInterface::class, new Request('GET', '/'));

ApplicationContext::setContainer($container);

$container->get(Hyperf\Contract\ApplicationInterface::class);
