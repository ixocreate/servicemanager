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
namespace KiwiSuiteTest\ServiceManager;

use KiwiSuite\ServiceManager\Exception\ServiceNotFoundException;
use KiwiSuite\ServiceManager\Resolver\ReflectionResolver;
use KiwiSuite\ServiceManager\ServiceManager;
use KiwiSuite\ServiceManager\ServiceManagerConfig;
use KiwiSuiteMisc\ServiceManager\DateTimeFactory;
use KiwiSuiteMisc\ServiceManager\ResolverTestObject;
use KiwiSuiteMisc\ServiceManager\ResolverTestObjectEmptyConstructor;
use KiwiSuiteMisc\ServiceManager\ResolverTestObjectNoConstructor;
use KiwiSuiteMisc\ServiceManager\ResolverTestObjectNoDep;
use KiwiSuiteMisc\ServiceManager\ResolverTestObjectScalar;
use KiwiSuiteMisc\ServiceManager\SubManagerFactory;
use PHPUnit\Framework\TestCase;

class ReflectionResolverTest extends TestCase
{
    public function testResolve()
    {
        $serviceManagerConfig = new ServiceManagerConfig([
            'factories' => [
                \DateTime::class => DateTimeFactory::class,
            ],
            'subManagers' => [
                'subManager1' => SubManagerFactory::class
            ]
        ]);

        $serviceManger = new ServiceManager($serviceManagerConfig);

        $resolver = new ReflectionResolver();
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
            ]
        ], $resolution->getDependencies());
    }

    public function testResolveNoConstructor()
    {
        $serviceManger = new ServiceManager(new ServiceManagerConfig([]));

        $resolver = new ReflectionResolver();
        $resolution = $resolver->resolveService($serviceManger, ResolverTestObjectNoConstructor::class);

        $this->assertEquals(ResolverTestObjectNoConstructor::class, $resolution->getServiceName());
        $this->assertEquals([], $resolution->getDependencies());
    }

    public function testResolveEmptyConstructor()
    {
        $serviceManger = new ServiceManager(new ServiceManagerConfig([]));

        $resolver = new ReflectionResolver();
        $resolution = $resolver->resolveService($serviceManger, ResolverTestObjectEmptyConstructor::class);

        $this->assertEquals(ResolverTestObjectEmptyConstructor::class, $resolution->getServiceName());
        $this->assertEquals([], $resolution->getDependencies());
    }

    public function testClassNotFound()
    {
        $this->expectException(ServiceNotFoundException::class);
        $this->expectExceptionCode(100);
        $serviceManger = new ServiceManager(new ServiceManagerConfig([]));

        $resolver = new ReflectionResolver();
        $resolver->resolveService($serviceManger, "test");
    }

    public function testDependencyIsScalar()
    {
        $this->expectException(ServiceNotFoundException::class);
        $this->expectExceptionCode(200);
        $serviceManger = new ServiceManager(new ServiceManagerConfig([]));

        $resolver = new ReflectionResolver();
        $resolver->resolveService($serviceManger, ResolverTestObjectScalar::class);
    }

    public function testDependencyNotFound()
    {
        $this->expectException(ServiceNotFoundException::class);
        $this->expectExceptionCode(300);

        $serviceManagerConfig = new ServiceManagerConfig([
            'factories' => [
                \DateTime::class => DateTimeFactory::class,
            ],
            'subManagers' => [
                'subManager1' => SubManagerFactory::class
            ]
        ]);

        $serviceManger = new ServiceManager($serviceManagerConfig);

        $resolver = new ReflectionResolver();
        $resolver->resolveService($serviceManger, ResolverTestObjectNoDep::class);
    }

    public function testCreateInstance()
    {
        $serviceManagerConfig = new ServiceManagerConfig([
            'factories' => [
                \DateTime::class => DateTimeFactory::class,
            ],
            'subManagers' => [
                'subManager1' => SubManagerFactory::class
            ]
        ]);

        $serviceManger = new ServiceManager($serviceManagerConfig);

        $resolver = new ReflectionResolver();
        $resolution = $resolver->resolveService($serviceManger, ResolverTestObject::class);

        $result = $resolver->createInstance($serviceManger, $resolution);

        $this->assertInstanceOf(ResolverTestObject::class, $result);
    }
}
