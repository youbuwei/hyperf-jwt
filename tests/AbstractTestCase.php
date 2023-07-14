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

use Carbon\Carbon;
use Hyperf\Utils\ApplicationContext;
use PHPUnit\Framework\TestCase;
use Youbuwei\HyperfJwt\Claims\Factory;
use Youbuwei\HyperfJwt\Contracts\ManagerInterface;
use Youbuwei\HyperfJwt\ManagerFactory;

abstract class AbstractTestCase extends TestCase
{
    /**
     * @var int
     */
    protected $testNowTimestamp;

    /**
     * @var \Psr\Container\ContainerInterface
     */
    protected $container;

    /**
     * @var \Mockery\LegacyMockInterface|\Mockery\MockInterface|\Youbuwei\HyperfJwt\Contracts\ManagerInterface|\Youbuwei\HyperfJwt\Manager
     */
    protected $manager;

    /**
     * @var \Youbuwei\HyperfJwt\Claims\Factory
     */
    protected $claimFactory;

    public function setUp(): void
    {
        parent::setUp();

        Carbon::setTestNow($now = Carbon::now());
        $this->testNowTimestamp = $now->getTimestamp();
        $this->container = ApplicationContext::getContainer();
        $this->container->set(ManagerInterface::class, $this->manager = \Mockery::mock(ManagerFactory::class));
        $this->manager->shouldReceive('getClaimFactory')->andReturn($this->claimFactory = new Factory(3600, 3600 * 24 * 14));
    }

    public function tearDown(): void
    {
        Carbon::setTestNow();
        \Mockery::close();

        parent::tearDown();
    }
}
