<?php
/**
 * @link https://github.com/ixocreate
 * @copyright IXOCREATE GmbH
 * @license MIT License
 */

declare(strict_types=1);

namespace Ixocreate\Test\ServiceManager;

use Ixocreate\ServiceManager\Factory\AutowireFactory;
use Ixocreate\ServiceManager\SubManager\SubManager;
use Ixocreate\Misc\ServiceManager\DateTimeFactory;
use Ixocreate\Misc\ServiceManager\Delegator2Factory;
use Ixocreate\Misc\ServiceManager\DelegatorFactory;
use Ixocreate\Misc\ServiceManager\Initializer;
use Ixocreate\Misc\ServiceManager\Initializer2;
use Ixocreate\Misc\ServiceManager\LazyLoadingObject;
use Ixocreate\Misc\ServiceManager\Scan\AbstractClass;
use Ixocreate\Misc\ServiceManager\Scan\Class1;
use Ixocreate\Misc\ServiceManager\Scan\Class2;
use Ixocreate\Misc\ServiceManager\Scan\Class4;
use Ixocreate\Misc\ServiceManager\Scan\SubDir\Class3;
use Ixocreate\Misc\ServiceManager\Scan\TestInterface;
use Ixocreate\Misc\ServiceManager\SubManagerFactory;
use PHPUnit\Framework\TestCase;

class ServiceManagerConfiguratorTest extends TestCase
{
    /**
     * @covers \Ixocreate\ServiceManager\AbstractServiceManagerConfigurator
     * @covers \Ixocreate\ServiceManager\ServiceManagerConfigurator
     */
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

    /**
     * @covers \Ixocreate\ServiceManager\AbstractServiceManagerConfigurator
     * @covers \Ixocreate\ServiceManager\ServiceManagerConfigurator
     */
    public function testServices()
    {
        $serviceManagerConfigurator = new ServiceManagerConfigurator();

        $factories = [
            'dateTime' => DateTimeFactory::class,
        ];

        $serviceManagerConfigurator->addService('dateTime', DateTimeFactory::class);

        $this->assertEquals($factories, $serviceManagerConfigurator->getFactories());
    }

    public function testDelegators()
    {
        $serviceManagerConfigurator = new ServiceManagerConfigurator();

        $delegators1 = [
            'test' => [DelegatorFactory::class],
        ];

        foreach ($delegators1 as $name => $value) {
            $serviceManagerConfigurator->addDelegator($name, $value);
        }

        $this->assertEquals($delegators1, $serviceManagerConfigurator->getDelegators());

        $delegators2 = [
            'test2' => [],
            'test' => [Delegator2Factory::class],
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
            LazyLoadingObject::class => null,
        ];

        foreach ($lazyServices as $name => $value) {
            if (empty($value)) {
                $serviceManagerConfigurator->addLazyService($name);
                continue;
            }
            $serviceManagerConfigurator->addLazyService($name, $value);
        }

        $lazyServices[LazyLoadingObject::class] = LazyLoadingObject::class;

        $this->assertEquals([
            'dateTime' => DateTimeFactory::class,
            LazyLoadingObject::class => LazyLoadingObject::class,
        ], $serviceManagerConfigurator->getLazyServices());
    }

    public function testInitializer()
    {
        $initializer = [
            Initializer::class,
            Initializer2::class,
        ];

        $serviceManagerConfigurator = new ServiceManagerConfigurator();

        foreach ($initializer as $value) {
            $serviceManagerConfigurator->addInitializer($value);
        }

        $this->assertEquals($initializer, $serviceManagerConfigurator->getInitializers());
    }

    /**
     * @covers \Ixocreate\ServiceManager\ServiceManagerConfigurator
     */
    public function testSubManagers()
    {
        $serviceManagerConfigurator = new ServiceManagerConfigurator();

        $subManagers = [
            SubManager::class => SubManagerFactory::class,
        ];

        foreach ($subManagers as $name => $value) {
            $serviceManagerConfigurator->addSubManager($name, $value);
        }

        $this->assertEquals($subManagers, $serviceManagerConfigurator->getSubManagers());
    }

    /**
     * @covers \Ixocreate\ServiceManager\ServiceManagerConfigurator
     */
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

    /**
     * @covers \Ixocreate\ServiceManager\ServiceManagerConfigurator
     */
    public function testGetServiceManagerConfig()
    {
        $serviceManagerConfigurator = new ServiceManagerConfigurator();
        $serviceManagerConfigurator->addInitializer(Initializer::class);
        $serviceManagerConfigurator->addSubManager(SubManager::class, SubManagerFactory::class);
        $serviceManagerConfigurator->addLazyService(\DateTime::class);
        $serviceManagerConfigurator->addDelegator("test", [DelegatorFactory::class]);
        $serviceManagerConfigurator->addFactory("factory", DateTimeFactory::class);

        $serviceManagerConfig = $serviceManagerConfigurator->getServiceManagerConfig();

        $this->assertInstanceOf(ServiceManagerConfig::class, $serviceManagerConfig);

        $this->assertEquals($serviceManagerConfigurator->getInitializers(), $serviceManagerConfig->getInitializers());
        $this->assertEquals($serviceManagerConfigurator->getFactories(), $serviceManagerConfig->getFactories());
        $this->assertEquals($serviceManagerConfigurator->getSubManagers(), $serviceManagerConfig->getSubManagers());
        $this->assertEquals($serviceManagerConfigurator->getLazyServices(), $serviceManagerConfig->getLazyServices());
        $this->assertEquals($serviceManagerConfigurator->getDelegators(), $serviceManagerConfig->getDelegators());
    }
}
