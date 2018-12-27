<?php
/**
 * @link https://github.com/ixocreate
 * @copyright IXOCREATE GmbH
 * @license MIT License
 */

declare(strict_types=1);

namespace IxocreateTest\ServiceManager;

use Ixocreate\ServiceManager\Autowire\FactoryResolver\FileFactoryResolver;
use Ixocreate\ServiceManager\Autowire\FactoryResolver\RuntimeFactoryResolver;
use Ixocreate\ServiceManager\Exception\ServiceNotCreatedException;
use Ixocreate\ServiceManager\Exception\ServiceNotFoundException;
use Ixocreate\ServiceManager\ServiceManager;
use Ixocreate\ServiceManager\ServiceManagerConfig;
use Ixocreate\ServiceManager\ServiceManagerConfigurator;
use Ixocreate\ServiceManager\ServiceManagerSetup;
use IxocreateMisc\ServiceManager\CantCreateObjectFactory;
use IxocreateMisc\ServiceManager\DateTimeFactory;
use IxocreateMisc\ServiceManager\LazyLoadingObject;
use IxocreateMisc\ServiceManager\TestInterface;
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

    public function testInitialServices()
    {
        $services = [
            \DateTime::class => new \DateTime(),
        ];
        $serviceManager = new ServiceManager($this->serviceManagerConfig, $this->serviceManagerSetup, $services);

        $this->assertSame($services[\DateTime::class], $serviceManager->get(\DateTime::class));
    }

    public function testGetServiceManagerConfig()
    {
        $this->assertEquals($this->serviceManagerConfig, $this->validServiceManager->getServiceManagerConfig());
    }

    public function testGetServiceManagerSetup()
    {
        $this->assertEquals($this->serviceManagerSetup, $this->validServiceManager->getServiceManagerSetup());
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

        $factories = [
            'test' => CantCreateObjectFactory::class,
        ];
        $serviceManagerConfig = new ServiceManagerConfig($factories);
        $serviceManager = new ServiceManager($serviceManagerConfig, new ServiceManagerSetup());

        $serviceManager->build("test");
    }

    public function testGetFactoryResolverPersist()
    {
        $serviceManager = new ServiceManager(new ServiceManagerConfig([]), new ServiceManagerSetup(null, null, true));
        $this->assertInstanceOf(FileFactoryResolver::class, $serviceManager->getFactoryResolver());
    }

    public function testGetFactoryResolverRuntime()
    {
        $serviceManager = new ServiceManager(new ServiceManagerConfig([]), new ServiceManagerSetup());
        $this->assertInstanceOf(RuntimeFactoryResolver::class, $serviceManager->getFactoryResolver());
    }
}
