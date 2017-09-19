<?php
/**
 * kiwi-suite/servicemanager (https://github.com/kiwi-suite/servicemanager)
 *
 * @package kiwi-suite/servicemanager
 * @see https://github.com/kiwi-suite/servicemanager
 * @copyright Copyright (c) 2010 - 2017 kiwi suite GmbH
 * @license MIT License
 */

declare(strict_types=1);
namespace KiwiSuiteTest\ServiceManager\Resolver;

use KiwiSuite\ServiceManager\Exception\ServiceNotFoundException;
use KiwiSuite\ServiceManager\Factory\AutowireFactory;
use KiwiSuite\ServiceManager\Generator\AutowireCacheGenerator;
use KiwiSuite\ServiceManager\Resolver\CacheResolver;
use KiwiSuite\ServiceManager\ServiceManager;
use KiwiSuite\ServiceManager\ServiceManagerConfig;
use KiwiSuite\ServiceManager\ServiceManagerSetup;
use KiwiSuiteMisc\ServiceManager\DateTimeFactory;
use KiwiSuiteMisc\ServiceManager\ResolverTestObject;
use KiwiSuiteMisc\ServiceManager\SubManagerFactory;
use KiwiSuiteTest\ServiceManager\CleanUpTrait;
use PHPUnit\Framework\TestCase;

class CacheResolverTest extends TestCase
{
    use CleanUpTrait;
    /**
     * @var ServiceManager
     */
    private $serviceManager;

    public function setUp()
    {
        $serviceManagerConfig = new ServiceManagerConfig([
            'factories' => [
                \DateTime::class => DateTimeFactory::class,
                'someThing' => DateTimeFactory::class,
                ResolverTestObject::class => AutowireFactory::class,
            ],
            'subManagers' => [
                'subManager1' => SubManagerFactory::class,
            ],
        ]);

        $this->serviceManager = new ServiceManager($serviceManagerConfig, new ServiceManagerSetup([
            'autowireResolver' => CacheResolver::class,
        ]));

        $autowireCacheGenerator = new AutowireCacheGenerator();
        $autowireCacheGenerator->write($this->serviceManager, $autowireCacheGenerator->generate($this->serviceManager));
    }

    public function testResolve()
    {
        $resolver = new CacheResolver();
        $resolution = $resolver->resolveService($this->serviceManager, ResolverTestObject::class);

        $this->assertEquals(ResolverTestObject::class, $resolution->getServiceName());
        $this->assertEquals([
            [
                'serviceName' => \DateTime::class,
                'subManager' => null,
            ],
            [
                'serviceName' => "test1",
                'subManager' => "subManager1",
            ],
            [
                'serviceName' => "someThing",
                'subManager' => null,
            ],
        ], $resolution->getDependencies());
    }

    public function testServiceNotFoundException()
    {
        $this->expectException(ServiceNotFoundException::class);
        $resolver = new CacheResolver();
        $resolver->resolveService($this->serviceManager, "test");
    }

    public function testFileDoesntExists()
    {
        $this->tearDown();
        $this->expectException(ServiceNotFoundException::class);
        $resolver = new CacheResolver();
        $resolver->resolveService($this->serviceManager, ResolverTestObject::class);
    }

    public function testInvalidSerialization()
    {
        \file_put_contents($this->serviceManager->getServiceManagerSetup()->getAutowireCacheFileLocation(), "invalid");

        $this->expectException(ServiceNotFoundException::class);
        $resolver = new CacheResolver();
        $resolver->resolveService($this->serviceManager, ResolverTestObject::class);
    }
}
