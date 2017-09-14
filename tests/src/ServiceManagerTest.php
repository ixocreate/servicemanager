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
use KiwiSuite\ServiceManager\Resolver\InMemoryResolver;
use KiwiSuite\ServiceManager\ServiceManager;
use KiwiSuite\ServiceManager\ServiceManagerConfig;
use KiwiSuite\ServiceManager\ServiceManagerConfigurator;
use KiwiSuite\ServiceManager\ServiceManagerSetup;
use KiwiSuiteMisc\ServiceManager\CantCreateObjectFactory;
use KiwiSuiteMisc\ServiceManager\DateTimeFactory;
use KiwiSuiteMisc\ServiceManager\LazyLoadingObject;
use KiwiSuiteMisc\ServiceManager\TestInterface;
use PHPUnit\Framework\TestCase;

class ServiceManagerTest extends TestCase
{
    /**
     * @var ServiceManager
     */
    private $validServiceManager;

    /**
     * @var ServiceManagerConfig
     */
    private $serviceManagerConfig;

    /**
     * @var ServiceManagerSetup
     */
    private $serviceManagerSetup;

    public function setUp()
    {
        $serviceManagerConfigurator = new ServiceManagerConfigurator();
        $serviceManagerConfigurator->addFactory(LazyLoadingObject::class);
        $serviceManagerConfigurator->addFactory('dateTine', DateTimeFactory::class);
        $serviceManagerConfigurator->addFactory('cantCreate', CantCreateObjectFactory::class);

        $serviceManagerConfigurator->addLazyService(LazyLoadingObject::class);

        $this->serviceManagerConfig = $serviceManagerConfigurator->getServiceManagerConfig();
        $this->serviceManagerSetup = new ServiceManagerSetup();

        $this->validServiceManager = new ServiceManager($this->serviceManagerConfig, $this->serviceManagerSetup);
    }

    public function testGetServiceManagerConfig()
    {
        $this->assertEquals($this->serviceManagerConfig, $this->validServiceManager->getServiceManagerConfig());
    }

    public function testGetServiceManagerSetup()
    {
        $this->assertEquals($this->serviceManagerSetup, $this->validServiceManager->getServiceManagerSetup());
    }

    public function testGetResolver()
    {
        $this->assertInstanceOf(InMemoryResolver::class, $this->validServiceManager->getResolver());
    }

    public function testHas()
    {
        $this->assertTrue($this->validServiceManager->has("dateTine"));
        $this->assertFalse($this->validServiceManager->has("doesnt_exist"));
    }

    public function testLazyLoading()
    {
        $result = $this->validServiceManager->get(LazyLoadingObject::class);

        $this->assertInstanceOf(LazyLoadingObject::class, $result);
        $this->assertTrue(\in_array(TestInterface::class, \class_implements($result)));
        $this->expectException(\Exception::class);
        $this->expectExceptionCode(500);
        $result->doSomething();
    }

    public function testServiceNotFoundExceptionGet()
    {
        $this->expectException(ServiceNotFoundException::class);
        $this->validServiceManager->get("doesnt_exists");
    }

    public function testServiceNotFoundExceptionBuild()
    {
        $this->expectException(ServiceNotFoundException::class);

        $this->validServiceManager->build("doesnt_exists");
    }

    public function testServiceNotCreatedExceptionGet()
    {
        $this->expectException(ServiceNotCreatedException::class);

        $this->validServiceManager->get("cantCreate");
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
        $serviceManager = new ServiceManager($serviceManagerConfig, new ServiceManagerSetup());

        $serviceManager->build("test");
    }
}
