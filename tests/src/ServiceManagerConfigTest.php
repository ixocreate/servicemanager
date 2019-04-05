<?php
/**
 * @link https://github.com/ixocreate
 * @copyright IXOCREATE GmbH
 * @license MIT License
 */

declare(strict_types=1);

namespace IxocreateTest\ServiceManager;

use Ixocreate\ServiceManager\Factory\AutowireFactory;
use Ixocreate\ServiceManager\ServiceManagerConfig;
use Ixocreate\ServiceManager\ServiceManagerConfigurator;
use Ixocreate\ServiceManager\SubManager\SubManager;
use IxocreateMisc\ServiceManager\CantCreateObjectFactory;
use IxocreateMisc\ServiceManager\DateTimeFactory;
use IxocreateMisc\ServiceManager\DelegatorFactory;
use IxocreateMisc\ServiceManager\Initializer;
use IxocreateMisc\ServiceManager\LazyLoadingObject;
use IxocreateMisc\ServiceManager\SubManagerFactory;
use PHPUnit\Framework\TestCase;
use Zend\ServiceManager\Proxy\LazyServiceFactory;

class ServiceManagerConfigTest extends TestCase
{
    public function testGetFactories()
    {
        $factories = [];
        $serviceManagerConfig = new ServiceManagerConfig(new ServiceManagerConfigurator());
        $this->assertEquals($factories, $serviceManagerConfig->getFactories());

        $factories = [
            'test' => DateTimeFactory::class,
        ];
        $serviceManagerConfigigurator = new ServiceManagerConfigurator();
        foreach ($factories as $key => $value) {
            $serviceManagerConfigigurator->addService($key, $value);
        }
        $this->assertEquals($factories, $serviceManagerConfigigurator->getServiceManagerConfig()->getFactories());
    }

    public function testGetSubManagers()
    {
        $subManagers = [];
        $serviceManagerConfig = new ServiceManagerConfig(new ServiceManagerConfigurator());
        $this->assertEquals($subManagers, $serviceManagerConfig->getSubManagers());

        $subManagers = [
            SubManager::class => SubManagerFactory::class,
        ];
        $serviceManagerConfigigurator = new ServiceManagerConfigurator();
        foreach ($subManagers as $key => $value) {
            $serviceManagerConfigigurator->addSubManager($key, $value);
        }
        $this->assertEquals($subManagers, $serviceManagerConfigigurator->getServiceManagerConfig()->getSubManagers());
    }

    public function testDelegators()
    {
        $delegators = [];
        $serviceManagerConfig = new ServiceManagerConfig(new ServiceManagerConfigurator());
        $this->assertEquals($delegators, $serviceManagerConfig->getDelegators());

        $delegators = [
            'test' => [DelegatorFactory::class],
        ];
        $serviceManagerConfigigurator = new ServiceManagerConfigurator();
        foreach ($delegators as $key => $value) {
            $serviceManagerConfigigurator->addDelegator($key, $value);
        }
        $this->assertEquals($delegators, $serviceManagerConfigigurator->getServiceManagerConfig()->getDelegators());
    }

    public function testGetLazyServices()
    {
        $lazyServices = [];
        $serviceManagerConfig = new ServiceManagerConfig(new ServiceManagerConfigurator());
        $this->assertEquals($lazyServices, $serviceManagerConfig->getLazyServices());

        $lazyServices = [
            'test' => \DateTime::class,
        ];
        $serviceManagerConfigigurator = new ServiceManagerConfigurator();
        foreach ($lazyServices as $key => $value) {
            $serviceManagerConfigigurator->addLazyService($key, $value);
        }
        $this->assertEquals($lazyServices, $serviceManagerConfigigurator->getServiceManagerConfig()->getLazyServices());
    }

    public function testGetInitializers()
    {
        $initializers = [];
        $serviceManagerConfig = new ServiceManagerConfig(new ServiceManagerConfigurator());
        $this->assertEquals($initializers, $serviceManagerConfig->getInitializers());

        $initializers = [
            Initializer::class,
        ];
        $serviceManagerConfigigurator = new ServiceManagerConfigurator();
        foreach ($initializers as $key => $value) {
            $serviceManagerConfigigurator->addInitializer($value);
        }
        $this->assertEquals($initializers, $serviceManagerConfigigurator->getServiceManagerConfig()->getInitializers());
    }

    public function testSerialize()
    {
        $serviceManagerConfigigurator = new ServiceManagerConfigurator();
        $serviceManagerConfigigurator->addService('test', DateTimeFactory::class);

        $serviceManagerConfig = new ServiceManagerConfig($serviceManagerConfigigurator);

        $this->assertEquals(\serialize([
            'factories' => [
                'test' => DateTimeFactory::class,
            ],
            'delegators' => [],
            'lazyServices' => [],
            'initializers' => [],
            'subManagers' => [],
            'metadata' => [],
            'namedServices' => [],
        ]), $serviceManagerConfig->serialize());
    }

    public function testUnserialize()
    {
        $items = [
            'factories' => [
                'test' => DateTimeFactory::class,
            ],
        ];
        $serviceManagerConfigurator = new ServiceManagerConfigurator();
        $serviceManagerConfigurator->addService('test', DateTimeFactory::class);

        $serviceManagerConfig = new ServiceManagerConfig($serviceManagerConfigurator);
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
                LazyLoadingObject::class => [LazyServiceFactory::class],
            ],
            'initializers' => [],
            'shared_by_default' => true,
        ], $serviceManagerConfig->getConfig());
    }
}
