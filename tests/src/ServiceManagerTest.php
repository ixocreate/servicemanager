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
use KiwiSuite\ServiceManager\ServiceManager;
use KiwiSuite\ServiceManager\ServiceManagerConfig;
use KiwiSuiteMisc\ServiceManager\CantCreateObjectFactory;
use PHPUnit\Framework\TestCase;

class ServiceManagerTest extends TestCase
{
    public function testConfigServiceGetRegistered()
    {
        $items = [
            'services' => [
                'test' => new \DateTime(),
            ],
        ];
        $serviceManagerConfig = new ServiceManagerConfig($items);

        $serviceManager = new ServiceManager($serviceManagerConfig);
        $this->assertEquals($serviceManagerConfig, $serviceManager->get(ServiceManagerConfig::class));
        $this->assertEquals($serviceManager, $serviceManager->get(ServiceManager::class));
    }

    public function testHas()
    {
        $items = [
            'services' => [
                'test' => new \DateTime(),
            ],
        ];
        $serviceManagerConfig = new ServiceManagerConfig($items);
        $serviceManager = new ServiceManager($serviceManagerConfig);

        $this->assertTrue($serviceManager->has("test"));
        $this->assertFalse($serviceManager->has("doesnt_exist"));
    }

    public function testServiceNotFoundExceptionGet()
    {
        $this->expectException(ServiceNotFoundException::class);

        $items = [
            'services' => [
                'test' => new \DateTime(),
            ],
        ];
        $serviceManagerConfig = new ServiceManagerConfig($items);
        $serviceManager = new ServiceManager($serviceManagerConfig);

        $serviceManager->get("doesnt_exists");
    }

    public function testServiceNotFoundExceptionBuild()
    {
        $this->expectException(ServiceNotFoundException::class);

        $items = [
            'services' => [
                'test' => new \DateTime(),
            ],
        ];
        $serviceManagerConfig = new ServiceManagerConfig($items);
        $serviceManager = new ServiceManager($serviceManagerConfig);

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
        $serviceManager = new ServiceManager($serviceManagerConfig);

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
        $serviceManager = new ServiceManager($serviceManagerConfig);

        $serviceManager->build("test");
    }
}
