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
use KiwiSuite\ServiceManager\FactoryInterface;
use KiwiSuite\ServiceManager\ServiceManager;
use KiwiSuite\ServiceManager\ServiceManagerConfig;
use KiwiSuite\ServiceManager\SubManager\SubManager;
use KiwiSuiteMisc\ServiceManager\CantCreateObjectFactory;
use KiwiSuiteMisc\ServiceManager\DateTimeFactory;
use PHPUnit\Framework\TestCase;

class SubManagerTest extends TestCase
{
    private function getServiceManager()
    {
        return new ServiceManager(new ServiceManagerConfig([]));
    }

    public function testGet()
    {
        $items = [
            'factories' => [
                'dateTime' => DateTimeFactory::class,
            ],
        ];
        $serviceManagerConfig = new ServiceManagerConfig($items);

        $serviceManager = new SubManager(
            $this->getServiceManager(),
            $serviceManagerConfig,
            \DateTimeInterface::class
        );
        $this->assertInstanceOf(\DateTimeInterface::class, $serviceManager->get("dateTime"));
    }

    public function testBuild()
    {
        $items = [
            'factories' => [
                'dateTime' => DateTimeFactory::class,
            ],
        ];
        $serviceManagerConfig = new ServiceManagerConfig($items);

        $serviceManager = new SubManager(
            $this->getServiceManager(),
            $serviceManagerConfig,
            \DateTimeInterface::class
        );
        $this->assertInstanceOf(\DateTimeInterface::class, $serviceManager->build("dateTime"));
    }

    public function testHas()
    {
        $items = [
            'factories' => [
                'test' => DateTimeFactory::class
            ],
        ];
        $serviceManagerConfig = new ServiceManagerConfig($items);
        $serviceManager = new SubManager(
            $this->getServiceManager(),
            $serviceManagerConfig,
            \DateTimeInterface::class
        );

        $this->assertTrue($serviceManager->has("test"));
        $this->assertFalse($serviceManager->has("doesnt_exist"));
    }

    public function testServiceNotFoundExceptionGet()
    {
        $this->expectException(ServiceNotFoundException::class);

        $serviceManagerConfig = new ServiceManagerConfig([]);
        $serviceManager = new SubManager(
            $this->getServiceManager(),
            $serviceManagerConfig,
            \DateTimeInterface::class
        );

        $serviceManager->get("doesnt_exists");
    }

    public function testServiceNotFoundExceptionBuild()
    {
        $this->expectException(ServiceNotFoundException::class);

        $serviceManagerConfig = new ServiceManagerConfig([]);
        $serviceManager = new SubManager(
            $this->getServiceManager(),
            $serviceManagerConfig,
            \DateTimeInterface::class
        );

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
        $serviceManager = new SubManager(
            $this->getServiceManager(),
            $serviceManagerConfig,
            \DateTimeInterface::class
        );

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
        $serviceManager = new SubManager(
            $this->getServiceManager(),
            $serviceManagerConfig,
            \DateTimeInterface::class
        );

        $serviceManager->build("test");
    }

    public function testValidateBuild()
    {
        $this->expectException(ServiceNotCreatedException::class);

        $items = [
            'factories' => [
                'test' => DateTimeFactory::class,
            ],
        ];
        $serviceManagerConfig = new ServiceManagerConfig($items);

        $serviceManager = new SubManager(
            $this->getServiceManager(),
            $serviceManagerConfig,
            FactoryInterface::class
        );

        $serviceManager->build("test");
    }

    public function testValidateGet()
    {
        $this->expectException(ServiceNotCreatedException::class);

        $items = [
            'factories' => [
                'test' => DateTimeFactory::class,
            ],
        ];
        $serviceManagerConfig = new ServiceManagerConfig($items);

        $serviceManager = new SubManager(
            $this->getServiceManager(),
            $serviceManagerConfig,
            FactoryInterface::class
        );

        $serviceManager->get("test");
    }
}
