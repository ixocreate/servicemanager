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
namespace KiwiSuiteTest\ServiceManager\Factory;

use KiwiSuite\ServiceManager\Exception\ServiceNotFoundException;
use KiwiSuite\ServiceManager\Factory\AutowireFactory;
use KiwiSuite\ServiceManager\Factory\LazyServiceDelegatorFactory;
use KiwiSuite\ServiceManager\ServiceManager;
use KiwiSuite\ServiceManager\ServiceManagerConfigurator;
use KiwiSuite\ServiceManager\ServiceManagerSetup;
use KiwiSuiteMisc\ServiceManager\LazyLoadingObject;
use KiwiSuiteMisc\ServiceManager\TestInterface;
use PHPUnit\Framework\TestCase;
use ProxyManager\Proxy\LazyLoadingInterface;
use ProxyManager\Proxy\ProxyInterface;
use ProxyManager\Proxy\ValueHolderInterface;
use ProxyManager\Proxy\VirtualProxyInterface;

class LazyServiceDelegatorFactoryTest extends TestCase
{
    /**
     * @var ServiceManager
     */
    private $serviceManager;

    public function setUp()
    {
        $serviceManagerConfigurator = new ServiceManagerConfigurator();
        $serviceManagerConfigurator->addFactory(LazyLoadingObject::class);
        $serviceManagerConfigurator->addLazyService(LazyLoadingObject::class);

        $this->serviceManager = new ServiceManager($serviceManagerConfigurator->getServiceManagerConfig(), new ServiceManagerSetup());
    }

    public function testInvoke()
    {
        $creationCallback = function () {
            $factory = new AutowireFactory();
            return $factory($this->serviceManager, LazyLoadingObject::class);
        };

        $lazyServiceFactory = new LazyServiceDelegatorFactory();
        $result = $lazyServiceFactory($this->serviceManager, LazyLoadingObject::class, $creationCallback);

        $this->assertInstanceOf(TestInterface::class, $result);
        $this->assertInstanceOf(ProxyInterface::class, $result);
        $this->assertInstanceOf(LazyLoadingInterface::class, $result);
        $this->assertInstanceOf(VirtualProxyInterface::class, $result);
        $this->assertInstanceOf(ValueHolderInterface::class, $result);
    }

    public function testException()
    {
        $this->expectException(ServiceNotFoundException::class);

        $creationCallback = function () {
            $factory = new AutowireFactory();
            return $factory($this->serviceManager, LazyLoadingObject::class);
        };

        $lazyServiceFactory = new LazyServiceDelegatorFactory();
        $lazyServiceFactory($this->serviceManager, \DateTime::class, $creationCallback);
    }
}
