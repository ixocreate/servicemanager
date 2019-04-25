<?php
/**
 * @link https://github.com/ixocreate
 * @copyright IXOCREATE GmbH
 * @license MIT License
 */

declare(strict_types=1);

namespace Ixocreate\Test\ServiceManager;

use Ixocreate\Misc\ServiceManager\CantCreateObjectFactory;
use Ixocreate\Misc\ServiceManager\DateTimeFactory;
use Ixocreate\Misc\ServiceManager\LazyLoadingObject;
use Ixocreate\Misc\ServiceManager\TestInterface;
use Ixocreate\ServiceManager\Autowire\FactoryResolver\FileFactoryResolver;
use Ixocreate\ServiceManager\Autowire\FactoryResolver\RuntimeFactoryResolver;
use Ixocreate\ServiceManager\Exception\ServiceNotCreatedException;
use Ixocreate\ServiceManager\Exception\ServiceNotFoundException;
use Ixocreate\ServiceManager\Factory\AutowireFactory;
use Ixocreate\ServiceManager\ServiceManager;
use Ixocreate\ServiceManager\ServiceManagerConfigInterface;
use Ixocreate\ServiceManager\ServiceManagerSetup;
use PHPUnit\Framework\TestCase;
use Zend\ServiceManager\Proxy\LazyServiceFactory;

class ServiceManagerTest extends TestCase
{
    /**
     * @var ServiceManager
     */
    private $validServiceManager;

    /**
     * @var ServiceManagerConfigInterface
     */
    private $serviceManagerConfig;

    /**
     * @var ServiceManagerSetup
     */
    private $serviceManagerSetup;

    public function setUp()
    {
        $factories = [
            LazyLoadingObject::class => AutowireFactory::class,
            'dateTine' => DateTimeFactory::class,
            'cantCreate' => CantCreateObjectFactory::class,
        ];
        $delegators = [
            LazyLoadingObject::class => [LazyServiceFactory::class],
        ];

        $serviceManagerConfig = $this->createMock(ServiceManagerConfigInterface::class);
        $serviceManagerConfig
            ->method('getFactories')
            ->willReturn($factories);

        $serviceManagerConfig
            ->method('getDelegators')
            ->willReturn($delegators);

        $serviceManagerConfig
            ->method('getLazyServices')
            ->willReturn([
                LazyLoadingObject::class => LazyLoadingObject::class,
            ]);

        $serviceManagerConfig
            ->method('getConfig')
            ->willReturn([
                'factories' => $factories,
                'delegators' => $delegators,
                'initializers' => [],
                'shared_by_default' => true,
            ]);


        $this->serviceManagerConfig = $serviceManagerConfig;
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
        $serviceManagerConfig = $this->createMock(ServiceManagerConfigInterface::class);
        $serviceManagerConfig
            ->method('getFactories')
            ->willReturn($factories);

        $serviceManagerConfig
            ->method('getConfig')
            ->willReturn([
                'factories' => $factories,
                'delegators' => [],
                'initializers' => [],
                'shared_by_default' => true,
            ]);

        $serviceManager = new ServiceManager($serviceManagerConfig, new ServiceManagerSetup());

        $serviceManager->build("test");
    }

    public function testGetFactoryResolverPersist()
    {
        $serviceManager = new ServiceManager(
            $this->createMock(ServiceManagerConfigInterface::class),
            new ServiceManagerSetup(null, null, true)
        );
        $this->assertInstanceOf(FileFactoryResolver::class, $serviceManager->getFactoryResolver());
    }

    public function testGetFactoryResolverRuntime()
    {
        $serviceManager = new ServiceManager(
            $this->createMock(ServiceManagerConfigInterface::class),
            new ServiceManagerSetup()
        );
        $this->assertInstanceOf(RuntimeFactoryResolver::class, $serviceManager->getFactoryResolver());
    }
}
