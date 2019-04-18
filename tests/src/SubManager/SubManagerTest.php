<?php
/**
 * @link https://github.com/ixocreate
 * @copyright IXOCREATE GmbH
 * @license MIT License
 */

declare(strict_types=1);

namespace Ixocreate\Test\ServiceManager;

use Ixocreate\ServiceManager\FactoryInterface;
use Ixocreate\ServiceManager\Exception\ServiceNotCreatedException;
use Ixocreate\ServiceManager\Exception\ServiceNotFoundException;
use Ixocreate\ServiceManager\ServiceManager;
use Ixocreate\ServiceManager\ServiceManagerConfig;
use Ixocreate\ServiceManager\ServiceManagerConfigurator;
use Ixocreate\ServiceManager\ServiceManagerSetup;
use Ixocreate\ServiceManager\SubManager\SubManager;
use Ixocreate\Misc\ServiceManager\CantCreateObjectFactory;
use Ixocreate\Misc\ServiceManager\DateTimeFactory;
use PHPUnit\Framework\TestCase;

class SubManagerTest extends TestCase
{
    /**
     * @var ServiceManager
     */
    private $serviceManager;

    /**
     * @var SubManager
     */
    private $subManager;

    /**
     * @var ServiceManagerConfig
     */
    private $subManagerConfig;

    public function setUp()
    {
        $this->serviceManager = new ServiceManager(new ServiceManagerConfig(new ServiceManagerConfigurator()), new ServiceManagerSetup());
        $serviceManagerConfigurator = new ServiceManagerConfigurator();
        $serviceManagerConfigurator->addService('dateTime', DateTimeFactory::class);
        $serviceManagerConfigurator->addService('cantCreate', CantCreateObjectFactory::class);
        $this->subManagerConfig = new ServiceManagerConfig($serviceManagerConfigurator);

        $this->subManager = new SubManager(
            $this->serviceManager,
            $this->subManagerConfig,
            \DateTimeInterface::class
        );
    }

    public function testGet()
    {
        $this->assertInstanceOf(\DateTimeInterface::class, $this->subManager->get("dateTime"));
    }

    public function testGetValidation()
    {
        $this->assertEquals(\DateTimeInterface::class, $this->subManager->getValidation());
    }

    public function testBuild()
    {
        $this->assertInstanceOf(\DateTimeInterface::class, $this->subManager->build("dateTime"));
    }

    public function testHas()
    {
        $this->assertTrue($this->subManager->has("dateTime"));
        $this->assertFalse($this->subManager->has("doesnt_exist"));
    }

    public function testGetServiceManagerSetup()
    {
        $this->assertEquals($this->serviceManager->getServiceManagerSetup(), $this->subManager->getServiceManagerSetup());
    }

    public function testGetServiceManagerConfig()
    {
        $this->assertEquals($this->subManagerConfig, $this->subManager->getServiceManagerConfig());
    }

    public function testServiceNotFoundExceptionGet()
    {
        $this->expectException(ServiceNotFoundException::class);

        $this->subManager->get("doesnt_exists");
    }

    public function testServiceNotFoundExceptionBuild()
    {
        $this->expectException(ServiceNotFoundException::class);

        $this->subManager->build("doesnt_exists");
    }

    public function testServiceNotCreatedExceptionGet()
    {
        $this->expectException(ServiceNotCreatedException::class);

        $this->subManager->get("cantCreate");
    }

    public function testServiceNotCreatedExceptionBuild()
    {
        $this->expectException(ServiceNotCreatedException::class);

        $this->subManager->build("cantCreate");
    }

    public function testValidateBuild()
    {
        $this->expectException(ServiceNotCreatedException::class);

        $serviceManagerConfigurator = new ServiceManagerConfigurator();
        $serviceManagerConfigurator->addService("test", DateTimeFactory::class);
        $serviceManagerConfig = new ServiceManagerConfig($serviceManagerConfigurator);

        $serviceManager = new SubManager(
            $this->serviceManager,
            $serviceManagerConfig,
            FactoryInterface::class
        );

        $serviceManager->build("test");
    }

    public function testValidateGet()
    {
        $this->expectException(ServiceNotCreatedException::class);

        $serviceManagerConfigurator = new ServiceManagerConfigurator();
        $serviceManagerConfigurator->addService("test", DateTimeFactory::class);
        $serviceManagerConfig = new ServiceManagerConfig($serviceManagerConfigurator);

        $serviceManager = new SubManager(
            $this->serviceManager,
            $serviceManagerConfig,
            FactoryInterface::class
        );

        $serviceManager->get("test");
    }

    public function testGetFactoryResolver()
    {
        $this->assertSame($this->serviceManager->getFactoryResolver(), $this->subManager->getFactoryResolver());
    }
}
