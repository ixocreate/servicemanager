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
use KiwiSuite\ServiceManager\ServiceManagerSetup;
use KiwiSuiteMisc\ServiceManager\DateTimeFactory;
use KiwiSuiteMisc\ServiceManager\SubManagerFactory;
use KiwiSuiteMisc\ServiceManager\ResolverTestObject;
use PHPUnit\Framework\TestCase;

class AutowireFactoryTest extends TestCase
{
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

        $this->serviceManager = new ServiceManager($serviceManagerConfig, new ServiceManagerSetup());
    }

    public function testInvoke()
    {
        $autoWireFactory = new AutowireFactory();
        $result = $autoWireFactory($this->serviceManager, ResolverTestObject::class);

        $this->assertInstanceOf(ResolverTestObject::class, $result);
    }

    public function testGetResolution()
    {
        $autoWireFactory = new AutowireFactory();
        $resolution = $autoWireFactory->getResolution($this->serviceManager, ResolverTestObject::class);
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
