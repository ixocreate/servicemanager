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
namespace KiwiSuiteTest\ServiceManager\Factory;

use KiwiSuite\ServiceManager\Factory\AutowireFactory;
use KiwiSuite\ServiceManager\ServiceManager;
use KiwiSuite\ServiceManager\ServiceManagerConfig;
use KiwiSuiteMisc\ServiceManager\DateTimeFactory;
use KiwiSuiteMisc\ServiceManager\SubManagerFactory;
use KiwiSuiteMisc\ServiceManager\ResolverTestObject;
use PHPUnit\Framework\TestCase;

class AutowireFactoryTest extends TestCase
{
    public function testInvoke()
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

        $serviceManger = new ServiceManager($serviceManagerConfig);

        $autoWireFactory = new AutowireFactory();
        $result = $autoWireFactory($serviceManger, ResolverTestObject::class);

        $this->assertInstanceOf(ResolverTestObject::class, $result);
    }

    public function testGetResolution()
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

        $serviceManger = new ServiceManager($serviceManagerConfig);

        $autoWireFactory = new AutowireFactory();
        $resolution = $autoWireFactory->getResolution($serviceManger, ResolverTestObject::class);
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
}
