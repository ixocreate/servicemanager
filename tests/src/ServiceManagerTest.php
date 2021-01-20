<?php
/**
 * @link https://github.com/ixocreate
 * @copyright IXOLIT GmbH
 * @license MIT License
 */

declare(strict_types=1);

namespace Ixocreate\Test\ServiceManager;

use Ixocreate\Misc\ServiceManager\CantCreateObjectFactory;
use Ixocreate\Misc\ServiceManager\DateTimeFactory;
use Ixocreate\Misc\ServiceManager\LazyLoadingObject;
use Ixocreate\Misc\ServiceManager\ServiceManagerConfig;
use Ixocreate\ServiceManager\Autowire\FactoryResolver\FileFactoryResolver;
use Ixocreate\ServiceManager\Autowire\FactoryResolver\RuntimeFactoryResolver;
use Ixocreate\ServiceManager\Exception\ServiceNotCreatedException;
use Ixocreate\ServiceManager\Exception\ServiceNotFoundException;
use Ixocreate\ServiceManager\Factory\AutowireFactory;
use Ixocreate\ServiceManager\ServiceManager;
use Ixocreate\ServiceManager\ServiceManagerConfigInterface;
use Ixocreate\ServiceManager\ServiceManagerSetup;
use Laminas\ServiceManager\Proxy\LazyServiceFactory;
use PHPUnit\Framework\TestCase;

/**
 * Class ServiceManagerTest
 * @package Ixocreate\Test\ServiceManager
 * @covers \Ixocreate\ServiceManager\ServiceManager
 */
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

    public function setUp(): void
    {
        $factories = [
            LazyLoadingObject::class => AutowireFactory::class,
            'dateTime' => DateTimeFactory::class,
            'cantCreate' => CantCreateObjectFactory::class,
        ];
        $delegators = [
            LazyLoadingObject::class => [LazyServiceFactory::class],
        ];
        $lazyServices = [
            LazyLoadingObject::class => LazyLoadingObject::class,
        ];
        $namedServices = [
            'dateTimeNamed' => 'dateTime',
        ];

        $serviceManagerConfig = new ServiceManagerConfig($factories, $delegators, [], $lazyServices, $namedServices);

        $this->serviceManagerConfig = $serviceManagerConfig;
        $this->serviceManagerSetup = new ServiceManagerSetup();

        $this->validServiceManager = new ServiceManager($this->serviceManagerConfig, $this->serviceManagerSetup);
    }

    public function testGetServiceManagerConfig()
    {
        $this->assertEquals($this->serviceManagerConfig, $this->validServiceManager->serviceManagerConfig());
    }

    public function testGetServiceManagerSetup()
    {
        $this->assertEquals($this->serviceManagerSetup, $this->validServiceManager->serviceManagerSetup());
    }

    public function testHas()
    {
        $this->assertTrue($this->validServiceManager->has('dateTime'));
        $this->assertFalse($this->validServiceManager->has('doesnt_exist'));
    }

    public function testServiceNotFoundExceptionGet()
    {
        $this->expectException(ServiceNotFoundException::class);
        $this->validServiceManager->get('doesnt_exists');
    }

    public function testServiceNotFoundExceptionBuild()
    {
        $this->expectException(ServiceNotFoundException::class);
        $this->validServiceManager->build('doesnt_exists');
    }

    public function testServiceNotCreatedExceptionGet()
    {
        $this->expectException(ServiceNotCreatedException::class);
        $this->validServiceManager->get('cantCreate');
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

        $serviceManager = new ServiceManager($serviceManagerConfig, new ServiceManagerSetup());

        $serviceManager->build('test');
    }

    public function testNamedService()
    {
        $namedDate = $this->validServiceManager->get('dateTimeNamed');
        $date = $this->validServiceManager->get('dateTime');

        $this->assertSame($date, $namedDate);
    }

    public function testGetFactoryResolverPersist()
    {
        $serviceManager = new ServiceManager(
            $this->createMock(ServiceManagerConfigInterface::class),
            new ServiceManagerSetup(null, null, true)
        );
        $this->assertInstanceOf(FileFactoryResolver::class, $serviceManager->factoryResolver());
    }

    public function testGetFactoryResolverRuntime()
    {
        $serviceManager = new ServiceManager(
            $this->createMock(ServiceManagerConfigInterface::class),
            new ServiceManagerSetup()
        );
        $this->assertInstanceOf(RuntimeFactoryResolver::class, $serviceManager->factoryResolver());
    }

    public function testServices()
    {
        $serviceManager = new ServiceManager($this->serviceManagerConfig, $this->serviceManagerSetup);

        $servicerNames = [
            LazyLoadingObject::class,
            'dateTime',
            'cantCreate',
        ];

        $this->assertEquals($servicerNames, $serviceManager->services());
    }

    public function testInitialServices()
    {
        $services = [
            \DateTime::class => new \DateTime(),
        ];
        $serviceManager = new ServiceManager($this->serviceManagerConfig, $this->serviceManagerSetup, $services);

        $this->assertSame($services, $serviceManager->initialServices());
    }
}
