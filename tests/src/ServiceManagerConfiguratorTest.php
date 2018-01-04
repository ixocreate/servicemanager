<?php
/**
 * kiwi-suite/servicemanager (https://github.com/kiwi-suite/servicemanager)
 *
 * @package kiwi-suite/servicemanager
 * @see https://github.com/kiwi-suite/servicemanager
 * @copyright Copyright (c) 2010 - 2018 kiwi suite GmbH
 * @license MIT License
 */

declare(strict_types=1);
namespace KiwiSuiteTest\ServiceManager;

use KiwiSuite\ServiceManager\Factory\AutowireFactory;
use KiwiSuite\ServiceManager\Factory\LazyServiceDelegatorFactory;
use KiwiSuite\ServiceManager\ServiceManagerConfig;
use KiwiSuite\ServiceManager\ServiceManagerConfigurator;
use KiwiSuiteMisc\ServiceManager\ConfigProvider;
use KiwiSuiteMisc\ServiceManager\DateTimeFactory;
use KiwiSuiteMisc\ServiceManager\DelegatorFactory;
use KiwiSuiteMisc\ServiceManager\Initializer;
use KiwiSuiteMisc\ServiceManager\Scan\AbstractClass;
use KiwiSuiteMisc\ServiceManager\Scan\Class1;
use KiwiSuiteMisc\ServiceManager\Scan\Class2;
use KiwiSuiteMisc\ServiceManager\Scan\Class4;
use KiwiSuiteMisc\ServiceManager\Scan\SubDir\Class3;
use KiwiSuiteMisc\ServiceManager\Scan\TestInterface;
use KiwiSuiteMisc\ServiceManager\SubManagerFactory;
use PHPUnit\Framework\TestCase;

class ServiceManagerConfiguratorTest extends TestCase
{
    public function testFactories()
    {
        $serviceManagerConfigurator = new ServiceManagerConfigurator();

        $factories = [
            'dateTime' => DateTimeFactory::class,
            'testAutoWire' => null,
        ];

        foreach ($factories as $name => $value) {
            if (empty($value)) {
                $serviceManagerConfigurator->addFactory($name);
                continue;
            }
            $serviceManagerConfigurator->addFactory($name, $value);
        }

        $factories['testAutoWire'] = AutowireFactory::class;

        $this->assertEquals($factories, $serviceManagerConfigurator->getFactories());
    }

    public function testDelegators()
    {
        $serviceManagerConfigurator = new ServiceManagerConfigurator();

        $delegators1 = [
            'test' => ['test'],
        ];

        foreach ($delegators1 as $name => $value) {
            $serviceManagerConfigurator->addDelegator($name, $value);
        }

        $this->assertEquals($delegators1, $serviceManagerConfigurator->getDelegators());

        $delegators2 = [
            'test2' => [],
            'test' => ['test1'],
        ];

        foreach ($delegators2 as $name => $value) {
            $serviceManagerConfigurator->addDelegator($name, $value);
        }

        $this->assertEquals($delegators1 + $delegators2, $serviceManagerConfigurator->getDelegators());
    }

    public function testLazyServices()
    {
        $serviceManagerConfigurator = new ServiceManagerConfigurator();

        $lazyServices = [
            'dateTime' => DateTimeFactory::class,
            'testFallBack' => null,
        ];

        foreach ($lazyServices as $name => $value) {
            if (empty($value)) {
                $serviceManagerConfigurator->addLazyService($name);
                continue;
            }
            $serviceManagerConfigurator->addLazyService($name, $value);
        }

        $lazyServices['testFallBack'] = "testFallBack";

        $this->assertEquals($lazyServices, $serviceManagerConfigurator->getLazyServices());

        $this->assertEquals([
            'dateTime' => [LazyServiceDelegatorFactory::class],
            'testFallBack' => [LazyServiceDelegatorFactory::class],
        ], $serviceManagerConfigurator->getDelegators());
    }

    public function testInitializer()
    {
        $initializer = [
            'array',
            'dateTime',
        ];

        $serviceManagerConfigurator = new ServiceManagerConfigurator();

        foreach ($initializer as $value) {
            $serviceManagerConfigurator->addInitializer($value);
        }

        $this->assertEquals($initializer, $serviceManagerConfigurator->getInitializers());
    }

    public function testDisablingSharing()
    {
        $disableSharing = [
            'array',
            'dateTime',
        ];

        $serviceManagerConfigurator = new ServiceManagerConfigurator();

        foreach ($disableSharing as $value) {
            $serviceManagerConfigurator->disableSharingFor($value);
        }

        $this->assertEquals($disableSharing, $serviceManagerConfigurator->getDisableSharing());
    }

    public function testSubManagers()
    {
        $serviceManagerConfigurator = new ServiceManagerConfigurator();

        $subManagers = [
            'sub1' => SubManagerFactory::class,
        ];

        foreach ($subManagers as $name => $value) {
            $serviceManagerConfigurator->addSubManager($name, $value);
        }

        $this->assertEquals($subManagers, $serviceManagerConfigurator->getSubManagers());
    }

    public function testConfigProviders()
    {
        $configProviders = [
            ConfigProvider::class,
        ];

        $serviceManagerConfigurator = new ServiceManagerConfigurator();

        foreach ($configProviders as $value) {
            $serviceManagerConfigurator->addConfigProvider($value);
        }

        $this->assertEquals($configProviders, $serviceManagerConfigurator->getConfigProviders());
    }

    public function testDirectoryScan()
    {
        $serviceManagerConfigurator = new ServiceManagerConfigurator();
        $serviceManagerConfigurator->addDirectory(__DIR__ . '/../misc/Scan');
        $serviceManagerConfigurator->addDirectory(__DIR__ . '/../misc/doesnt_exist');
        $serviceManagerConfig = $serviceManagerConfigurator->getServiceManagerConfig();
        $this->assertArrayHasKey(Class1::class, $serviceManagerConfig->getFactories());
        $this->assertArrayHasKey(Class2::class, $serviceManagerConfig->getFactories());
        $this->assertArrayHasKey(Class3::class, $serviceManagerConfig->getFactories());
        $this->assertArrayNotHasKey(AbstractClass::class, $serviceManagerConfig->getFactories());
        $this->assertArrayNotHasKey(TestInterface::class, $serviceManagerConfig->getFactories());

        $serviceManagerConfigurator = new ServiceManagerConfigurator();
        $serviceManagerConfigurator->addDirectory(__DIR__ . '/../misc/Scan', false);
        $serviceManagerConfig = $serviceManagerConfigurator->getServiceManagerConfig();
        $this->assertArrayHasKey(Class1::class, $serviceManagerConfig->getFactories());
        $this->assertArrayHasKey(Class2::class, $serviceManagerConfig->getFactories());
        $this->assertArrayNotHasKey(Class3::class, $serviceManagerConfig->getFactories());

        $serviceManagerConfigurator = new ServiceManagerConfigurator();
        $serviceManagerConfigurator->addDirectory(__DIR__ . '/../misc/Scan', true, [AbstractClass::class, TestInterface::class]);
        $serviceManagerConfig = $serviceManagerConfigurator->getServiceManagerConfig();
        $this->assertArrayNotHasKey(Class1::class, $serviceManagerConfig->getFactories());
        $this->assertArrayHasKey(Class2::class, $serviceManagerConfig->getFactories());
        $this->assertArrayHasKey(Class3::class, $serviceManagerConfig->getFactories());
        $this->assertArrayHasKey(Class4::class, $serviceManagerConfig->getFactories());
    }

    public function testGetServiceManagerConfig()
    {
        $serviceManagerConfigurator = new ServiceManagerConfigurator();
        $serviceManagerConfigurator->addInitializer(Initializer::class);
        $serviceManagerConfigurator->addSubManager("subManager", SubManagerFactory::class);
        $serviceManagerConfigurator->addLazyService(\DateTime::class);
        $serviceManagerConfigurator->addDelegator("test", [DelegatorFactory::class]);
        $serviceManagerConfigurator->addFactory("factory", DateTimeFactory::class);
        $serviceManagerConfigurator->addConfigProvider(ConfigProvider::class);

        $serviceManagerConfig = $serviceManagerConfigurator->getServiceManagerConfig();

        $this->assertInstanceOf(ServiceManagerConfig::class, $serviceManagerConfig);

        $this->assertEquals($serviceManagerConfigurator->getInitializers(), $serviceManagerConfig->getInitializers());
        $this->assertEquals(\array_merge(
            ['dateTimeFromConfigProvider' => DateTimeFactory::class],
            $serviceManagerConfigurator->getFactories()
        ), $serviceManagerConfig->getFactories());
        $this->assertEquals($serviceManagerConfigurator->getSubManagers(), $serviceManagerConfig->getSubManagers());
        $this->assertEquals($serviceManagerConfigurator->getLazyServices(), $serviceManagerConfig->getLazyServices());
        $this->assertEquals($serviceManagerConfigurator->getDelegators(), $serviceManagerConfig->getDelegators());
        $this->assertEquals($serviceManagerConfigurator->getConfigProviders(), $serviceManagerConfig->getConfigProviders());
    }
}