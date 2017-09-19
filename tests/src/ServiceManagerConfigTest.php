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

use KiwiSuite\ServiceManager\Exception\InvalidArgumentException;
use KiwiSuite\ServiceManager\Factory\AutowireFactory;
use KiwiSuite\ServiceManager\Factory\LazyServiceDelegatorFactory;
use KiwiSuite\ServiceManager\ServiceManagerConfig;
use KiwiSuite\ServiceManager\ServiceManagerConfigurator;
use KiwiSuiteMisc\ServiceManager\CantCreateObjectFactory;
use KiwiSuiteMisc\ServiceManager\ConfigProvider;
use KiwiSuiteMisc\ServiceManager\DateTimeFactory;
use KiwiSuiteMisc\ServiceManager\DelegatorFactory;
use KiwiSuiteMisc\ServiceManager\Initializer;
use KiwiSuiteMisc\ServiceManager\LazyLoadingObject;
use KiwiSuiteMisc\ServiceManager\SubManagerFactory;
use PHPUnit\Framework\TestCase;

class ServiceManagerConfigTest extends TestCase
{
    public function testGetFactories()
    {
        $items = [];
        $serviceManagerConfig = new ServiceManagerConfig($items);
        $this->assertEquals($items, $serviceManagerConfig->getFactories());

        $items = [
            'factories' => [
                'test' => DateTimeFactory::class,
            ],
        ];
        $serviceManagerConfig = new ServiceManagerConfig($items);
        $this->assertEquals($items['factories'], $serviceManagerConfig->getFactories());
    }

    public function testGetSubManagers()
    {
        $items = [];
        $serviceManagerConfig = new ServiceManagerConfig($items);
        $this->assertEquals($items, $serviceManagerConfig->getSubManagers());

        $items = [
            'subManagers' => [
                'test' => SubManagerFactory::class,
            ],
        ];
        $serviceManagerConfig = new ServiceManagerConfig($items);
        $this->assertEquals($items['subManagers'], $serviceManagerConfig->getSubManagers());
    }

    public function testGetDisabledSharing()
    {
        $items = [];
        $serviceManagerConfig = new ServiceManagerConfig($items);
        $this->assertEquals($items, $serviceManagerConfig->getDisabledSharing());

        $items = [
            'disabledSharing' => [
                \DateTime::class,
            ],
        ];
        $serviceManagerConfig = new ServiceManagerConfig($items);
        $this->assertEquals($items['disabledSharing'], $serviceManagerConfig->getDisabledSharing());
    }

    public function testDelegators()
    {
        $items = [];
        $serviceManagerConfig = new ServiceManagerConfig($items);
        $this->assertEquals($items, $serviceManagerConfig->getDelegators());

        $items = [
            'delegators' => [
                'test' => [DelegatorFactory::class],
            ],
        ];
        $serviceManagerConfig = new ServiceManagerConfig($items);
        $this->assertEquals($items['delegators'], $serviceManagerConfig->getDelegators());
    }

    public function testGetInitializers()
    {
        $items = [];
        $serviceManagerConfig = new ServiceManagerConfig($items);
        $this->assertEquals($items, $serviceManagerConfig->getInitializers());

        $items = [
            'initializers' => [
                'test' => Initializer::class,
            ],
        ];
        $serviceManagerConfig = new ServiceManagerConfig($items);
        $this->assertEquals($items['initializers'], $serviceManagerConfig->getInitializers());
    }

    public function testGetLazyServices()
    {
        $items = [];
        $serviceManagerConfig = new ServiceManagerConfig($items);
        $this->assertEquals($items, $serviceManagerConfig->getLazyServices());

        $items = [
            'lazyServices' => [
                'test' => \DateTime::class,
            ],
        ];
        $serviceManagerConfig = new ServiceManagerConfig($items);
        $this->assertEquals($items['lazyServices'], $serviceManagerConfig->getLazyServices());
    }

    public function testGetConfigProviders()
    {
        $items = [];
        $serviceManagerConfig = new ServiceManagerConfig($items);
        $this->assertEquals($items, $serviceManagerConfig->getConfigProviders());

        $items = [
            'configProviders' => [
                ConfigProvider::class,
            ],
        ];
        $serviceManagerConfig = new ServiceManagerConfig($items);
        $this->assertEquals($items['configProviders'], $serviceManagerConfig->getConfigProviders());
    }

    public function testInvalidConfigKey()
    {
        $this->expectException(InvalidArgumentException::class);

        $items = [
            'invalid' => [],
        ];

        new ServiceManagerConfig($items);
    }

    public function testInvalidFactoryString()
    {
        $this->expectException(InvalidArgumentException::class);

        $items = [
            'factories' => [
                'test' => [],
            ],
        ];

        new ServiceManagerConfig($items);
    }

    public function testInvalidFactoryInvalid()
    {
        $this->expectException(InvalidArgumentException::class);

        $items = [
            'factories' => [
                'test' => "test",
            ],
        ];

        new ServiceManagerConfig($items);
    }

    public function testInvalidFactoryImplements()
    {
        $this->expectException(InvalidArgumentException::class);

        $items = [
            'factories' => [
                'test' => Initializer::class,
            ],
        ];

        new ServiceManagerConfig($items);
    }

    public function testInvalidSubManagersString()
    {
        $this->expectException(InvalidArgumentException::class);

        $items = [
            'subManagers' => [
                'test' => [],
            ],
        ];

        new ServiceManagerConfig($items);
    }

    public function testInvalidSubManagersInvalid()
    {
        $this->expectException(InvalidArgumentException::class);

        $items = [
            'subManagers' => [
                'test' => "test",
            ],
        ];

        new ServiceManagerConfig($items);
    }

    public function testInvalidSubManagersImplements()
    {
        $this->expectException(InvalidArgumentException::class);

        $items = [
            'subManagers' => [
                'test' => Initializer::class,
            ],
        ];

        new ServiceManagerConfig($items);
    }

    public function testInvalidInitializerString()
    {
        $this->expectException(InvalidArgumentException::class);

        $items = [
            'initializers' => [
                [],
            ],
        ];

        new ServiceManagerConfig($items);
    }

    public function testInvalidInitializerInvalid()
    {
        $this->expectException(InvalidArgumentException::class);

        $items = [
            'initializers' => [
                "test",
            ],
        ];

        new ServiceManagerConfig($items);
    }

    public function testInvalidInitializerImplements()
    {
        $this->expectException(InvalidArgumentException::class);

        $items = [
            'initializers' => [
                SubManagerFactory::class,
            ],
        ];

        new ServiceManagerConfig($items);
    }

    public function testInvalidDelegatorFactory()
    {
        $this->expectException(InvalidArgumentException::class);

        $items = [
            'delegators' => [
                'test' => "test",
            ],
        ];
        new ServiceManagerConfig($items);
    }

    public function testInvalidDelegatorStringFactory()
    {
        $this->expectException(InvalidArgumentException::class);

        $items = [
            'delegators' => [
                'test' => [new \DateTime()],
            ],
        ];
        new ServiceManagerConfig($items);
    }

    public function testInvalidDelegatorCantLoadFactory()
    {
        $this->expectException(InvalidArgumentException::class);

        $items = [
            'delegators' => [
                'test' => ["test"],
            ],
        ];
        new ServiceManagerConfig($items);
    }

    public function testInvalidDelegatorImplementsFactory()
    {
        $this->expectException(InvalidArgumentException::class);

        $items = [
            'delegators' => [
                'test' => [DateTimeFactory::class],
            ],
        ];
        new ServiceManagerConfig($items);
    }

    public function testInvalidConfigProvidersString()
    {
        $this->expectException(InvalidArgumentException::class);

        $items = [
            'configProviders' => [
                'test' => [],
            ],
        ];

        new ServiceManagerConfig($items);
    }

    public function testInvalidConfigProvidersInvalid()
    {
        $this->expectException(InvalidArgumentException::class);

        $items = [
            'configProviders' => [
                "test",
            ],
        ];

        new ServiceManagerConfig($items);
    }

    public function testInvalidConfigProvidersImplements()
    {
        $this->expectException(InvalidArgumentException::class);

        $items = [
            'configProviders' => [
                Initializer::class,
            ],
        ];

        new ServiceManagerConfig($items);
    }

    public function testSerialize()
    {
        $serviceManagerConfig = new ServiceManagerConfig([
            'factories' => [
                'test' => DateTimeFactory::class,
            ],
        ]);

        $this->assertEquals(\serialize([
            'factories' => [
                'test' => DateTimeFactory::class,
            ],
            'configProviders' => [],
            'disabledSharing' => [],
            'delegators' => [],
            'initializers' => [],
            'lazyServices' => [],
            'subManagers' => [],
        ]), $serviceManagerConfig->serialize());
    }

    public function testUnserialize()
    {
        $items = [
            'factories' => [
                'test' => DateTimeFactory::class,
            ],
        ];

        $serviceManagerConfig = new ServiceManagerConfig([]);
        $serviceManagerConfig->unserialize(\serialize($items));
        $this->assertEquals($items['factories'], $serviceManagerConfig->getFactories());
    }

    public function testGetConfig()
    {
        $serviceManagerConfigurator = new ServiceManagerConfigurator();
        $serviceManagerConfigurator->addFactory(LazyLoadingObject::class);
        $serviceManagerConfigurator->addFactory('dateTime', DateTimeFactory::class);
        $serviceManagerConfigurator->addFactory('cantCreate', CantCreateObjectFactory::class);

        $serviceManagerConfigurator->addLazyService(LazyLoadingObject::class);

        $serviceManagerConfig = $serviceManagerConfigurator->getServiceManagerConfig();

        $this->assertEquals([
            'factories' => [
                LazyLoadingObject::class => AutowireFactory::class,
                'dateTime' => DateTimeFactory::class,
                "cantCreate" => CantCreateObjectFactory::class,
            ],
            'delegators' => [
                LazyLoadingObject::class => [LazyServiceDelegatorFactory::class],
            ],
            'shared' => [],
            'initializers' => [],
            'shared_by_default' => true,
        ], $serviceManagerConfig->getConfig());
    }
}
