<?php
/**
 * kiwi-suite/servicemanager (https://github.com/kiwi-suite/servicemanager)
 *
 * @package kiwi-suite/servicemanager
 * @link https://github.com/kiwi-suite/servicemanager
 * @copyright Copyright (c) 2010 - 2018 kiwi suite GmbH
 * @license MIT License
 */

declare(strict_types=1);
namespace IxocreateTest\ServiceManager;

use Ixocreate\Contract\ServiceManager\FactoryInterface;
use Ixocreate\ServiceManager\Exception\ServiceNotCreatedException;
use Ixocreate\ServiceManager\Exception\ServiceNotFoundException;
use Ixocreate\ServiceManager\ServiceManager;
use Ixocreate\ServiceManager\ServiceManagerConfig;
use Ixocreate\ServiceManager\ServiceManagerSetup;
use Ixocreate\ServiceManager\SubManager\SubManager;
use IxocreateMisc\ServiceManager\CantCreateObjectFactory;
use IxocreateMisc\ServiceManager\DateTimeFactory;
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
        $this->serviceManager = new ServiceManager(new ServiceManagerConfig([]), new ServiceManagerSetup());

        $factories = [
            'dateTime' => DateTimeFactory::class,
            'cantCreate' => CantCreateObjectFactory::class,
        ];
        $this->subManagerConfig = new ServiceManagerConfig($factories);

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

        $factories = [
            'test' => DateTimeFactory::class,
        ];
        $serviceManagerConfig = new ServiceManagerConfig($factories);

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

        $factories = [
            'test' => DateTimeFactory::class,
        ];
        $serviceManagerConfig = new ServiceManagerConfig($factories);

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
