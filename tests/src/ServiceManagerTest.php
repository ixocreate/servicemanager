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

use KiwiSuite\ServiceManager\Exception\ServiceNotCreatedException;
use KiwiSuite\ServiceManager\Exception\ServiceNotFoundException;
use KiwiSuite\ServiceManager\ServiceManager;
use KiwiSuite\ServiceManager\ServiceManagerConfig;
use KiwiSuite\ServiceManager\ServiceManagerConfigurator;
use KiwiSuiteMisc\ServiceManager\CantCreateObjectFactory;
use KiwiSuiteMisc\ServiceManager\DateTimeFactory;
use KiwiSuiteMisc\ServiceManager\LazyLoadingObject;
use KiwiSuiteMisc\ServiceManager\TestInterface;
use PHPUnit\Framework\TestCase;
use ProxyManager\Factory\LazyLoadingValueHolderFactory;

class ServiceManagerTest extends TestCase
{
    public function testConfigServiceGetRegistered()
    {
        $items = [];
        $serviceManagerConfig = new ServiceManagerConfig($items);

        $serviceManager = new ServiceManager($serviceManagerConfig);
        $this->assertEquals($serviceManagerConfig, $serviceManager->get(ServiceManagerConfig::class));
        $this->assertEquals($serviceManager, $serviceManager->get(ServiceManager::class));
        $this->assertInstanceOf(LazyLoadingValueHolderFactory::class, $serviceManager->get(LazyLoadingValueHolderFactory::class));
    }

    public function testHas()
    {
        $items = [
            'factories' => [
                'test' => DateTimeFactory::class,
            ],
        ];
        $serviceManagerConfig = new ServiceManagerConfig($items);
        $serviceManager = new ServiceManager($serviceManagerConfig);

        $this->assertTrue($serviceManager->has("test"));
        $this->assertFalse($serviceManager->has("doesnt_exist"));
    }

    public function testLazyLoading()
    {
        $serviceManagerConfigurator = new ServiceManagerConfigurator();
        $serviceManagerConfigurator->addFactory(LazyLoadingObject::class);
        $serviceManagerConfigurator->addLazyService(LazyLoadingObject::class);

        $serviceManager = new ServiceManager($serviceManagerConfigurator->getServiceManagerConfig());
        $result = $serviceManager->get(LazyLoadingObject::class);

        $this->assertInstanceOf(LazyLoadingObject::class, $result);
        $this->assertTrue(in_array(TestInterface::class, class_implements($result)));

        $this->expectException(\Exception::class);
        $this->expectExceptionCode(500);
        $result->doSomething();
    }

    public function testServiceNotFoundExceptionGet()
    {
        $this->expectException(ServiceNotFoundException::class);

        $items = [
            'factories' => [
                'test' => DateTimeFactory::class,
            ],
        ];
        $serviceManagerConfig = new ServiceManagerConfig($items);
        $serviceManager = new ServiceManager($serviceManagerConfig);

        $serviceManager->get("doesnt_exists");
    }

    public function testServiceNotFoundExceptionBuild()
    {
        $this->expectException(ServiceNotFoundException::class);

        $items = [
            'factories' => [
                'test' => DateTimeFactory::class,
            ],
        ];
        $serviceManagerConfig = new ServiceManagerConfig($items);
        $serviceManager = new ServiceManager($serviceManagerConfig);

        $serviceManager->build("doesnt_exists");
    }

    public function testServiceNotCreatedExceptionGet()
    {
        $this->expectException(ServiceNotCreatedException::class);

        $items = [
            'factories' => [
                'test' => CantCreateObjectFactory::class,
            ],
        ];
        $serviceManagerConfig = new ServiceManagerConfig($items);
        $serviceManager = new ServiceManager($serviceManagerConfig);

        $serviceManager->get("test");
    }

    public function testServiceNotCreatedExceptionBuild()
    {
        $this->expectException(ServiceNotCreatedException::class);

        $items = [
            'factories' => [
                'test' => CantCreateObjectFactory::class,
            ],
        ];
        $serviceManagerConfig = new ServiceManagerConfig($items);
        $serviceManager = new ServiceManager($serviceManagerConfig);

        $serviceManager->build("test");
    }
}
