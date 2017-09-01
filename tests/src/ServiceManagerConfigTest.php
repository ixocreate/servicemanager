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
use KiwiSuite\ServiceManager\ServiceManagerConfig;
use KiwiSuiteMisc\ServiceManager\DateTimeFactory;
use KiwiSuiteMisc\ServiceManager\DelegatorFactory;
use KiwiSuiteMisc\ServiceManager\Initializer;
use KiwiSuiteMisc\ServiceManager\SubManagerFactory;
use PHPUnit\Framework\TestCase;

class ServiceManagerConfigTest extends TestCase
{
    public function testGetServices()
    {
        $items = [];
        $serviceManagerConfig = new ServiceManagerConfig($items);
        $this->assertEquals($items, $serviceManagerConfig->getServices());

        $items = [
            'services' => [
                'test' => new \DateTime(),
                'test1' => [],
            ],
        ];
        $serviceManagerConfig = new ServiceManagerConfig($items);
        $this->assertEquals($items['services'], $serviceManagerConfig->getServices());
    }

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

    public function testInvalidConfigKey()
    {
        $this->expectException(InvalidArgumentException::class);

        $items = [
            'invalid' => [],
        ];

        new ServiceManagerConfig($items);
    }

    public function testInvalidService()
    {
        $this->expectException(InvalidArgumentException::class);

        $items = [
            'services' => [
                'test' => 'string',
            ],
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

    public function testSerialize()
    {
        $items = [
            'factories' => [
                'test' => DateTimeFactory::class,
            ],
        ];
        $serviceManagerConfig = new ServiceManagerConfig($items);

        $this->assertEquals(\serialize($items), $serviceManagerConfig->serialize());
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
}
