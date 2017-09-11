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

use KiwiSuite\ServiceManager\Exception\InvalidArgumentException;
use KiwiSuite\ServiceManager\Resolver\ReflectionResolver;
use KiwiSuite\ServiceManager\Resolver\Resolution;
use KiwiSuite\ServiceManager\ServiceManager;
use KiwiSuite\ServiceManager\ServiceManagerConfig;
use KiwiSuite\ServiceManager\ServiceManagerSetup;
use KiwiSuiteMisc\ServiceManager\DateTimeFactory;
use KiwiSuiteMisc\ServiceManager\ResolverTestObject;
use KiwiSuiteMisc\ServiceManager\SubManagerFactory;
use PHPUnit\Framework\TestCase;

class ResolutionTest extends TestCase
{
    public function testResolution()
    {
        $dependencies= [
            [
                'serviceName' => 'test1',
                'subManager' => null,
            ],
            [
                'serviceName' => 'test1',
                'subManager' => 'subManager1',
            ],
        ];
        $resolution = new Resolution(
            "testResolution",
            $dependencies
        );

        $this->assertEquals("testResolution", $resolution->getServiceName());
        $this->assertEquals($dependencies, $resolution->getDependencies());
    }

    public function testCreateInstance()
    {
        $serviceManagerConfig = new ServiceManagerConfig([
            'factories' => [
                \DateTime::class => DateTimeFactory::class,
                'someThing' => DateTimeFactory::class,
            ],
            'subManagers' => [
                'subManager1' => SubManagerFactory::class,
            ],
        ]);

        $serviceManger = new ServiceManager($serviceManagerConfig, new ServiceManagerSetup());

        $resolver = new ReflectionResolver();
        $resolution = $resolver->resolveService($serviceManger, ResolverTestObject::class);

        $result = $resolution->createInstance($serviceManger);

        $this->assertInstanceOf(ResolverTestObject::class, $result);
    }

    public function testDependencyIsNotAnArray()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionCode(100);
        new Resolution(
            "testResolution",
            ['string']
        );
    }

    public function testDependencyServiceNameNotSet()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionCode(200);
        new Resolution(
            "testResolution",
            [[]]
        );
    }

    public function testDependencyServiceNameNotAString()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionCode(200);
        new Resolution(
            "testResolution",
            [['serviceName' => []]]
        );
    }

    public function testDependencySubManagerNotSet()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionCode(300);
        new Resolution(
            "testResolution",
            [['serviceName' => 'test']]
        );
    }

    public function testDependencySubManagerNotStringOrNull()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionCode(300);
        new Resolution(
            "testResolution",
            [['serviceName' => 'test', 'subManager' => []]]
        );
    }
}
