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
use KiwiSuite\ServiceManager\Resolver\InMemoryResolver;
use KiwiSuite\ServiceManager\ServiceManager;
use KiwiSuite\ServiceManager\ServiceManagerConfig;
use KiwiSuite\ServiceManager\ServiceManagerSetup;
use KiwiSuiteMisc\ServiceManager\DateTimeFactory;
use KiwiSuiteMisc\ServiceManager\ResolverTestObject;
use KiwiSuiteMisc\ServiceManager\SubManagerFactory;
use PHPUnit\Framework\TestCase;

class InMemoryResolverTest extends TestCase
{
    public function testResolve()
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

        $serviceManger = new ServiceManager($serviceManagerConfig, new ServiceManagerSetup());

        $resolver = new InMemoryResolver();
        $resolution = $resolver->resolveService($serviceManger, ResolverTestObject::class);

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
        $serviceManger = new ServiceManager(new ServiceManagerConfig([]), new ServiceManagerSetup());

        $resolver = new InMemoryResolver();
        $resolver->resolveService($serviceManger, "test");
    }
}
