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

use Youbuwei\HyperfJwt\Commands\GenJwtKeypairCommand;
use Youbuwei\HyperfJwt\Commands\GenJwtSecretCommand;
use Youbuwei\HyperfJwt\Contracts\JwtFactoryInterface;
use Youbuwei\HyperfJwt\Contracts\ManagerInterface;
use Youbuwei\HyperfJwt\Contracts\PayloadValidatorInterface;
use Youbuwei\HyperfJwt\Contracts\RequestParser\RequestParserInterface;
use Youbuwei\HyperfJwt\Contracts\TokenValidatorInterface;
use Youbuwei\HyperfJwt\RequestParser\RequestParserFactory;
use Youbuwei\HyperfJwt\Validators\PayloadValidator;
use Youbuwei\HyperfJwt\Validators\TokenValidator;

class ConfigProvider
{
    public function __invoke(): array
    {
        return [
            'dependencies' => [
                ManagerInterface::class => ManagerFactory::class,
                TokenValidatorInterface::class => TokenValidator::class,
                PayloadValidatorInterface::class => PayloadValidator::class,
                RequestParserInterface::class => RequestParserFactory::class,
                JwtFactoryInterface::class => JwtFactory::class,
            ],
            'commands' => [
                GenJwtSecretCommand::class,
                GenJwtKeypairCommand::class,
            ],
            'publish' => [
                [
                    'id' => 'config',
                    'description' => 'The config for youbuwei/hyperf-jwt.',
                    'source' => __DIR__ . '/../publish/jwt.php',
                    'destination' => BASE_PATH . '/config/autoload/jwt.php',
                ],
            ],
        ];
    }
}
