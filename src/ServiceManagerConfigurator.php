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
namespace KiwiSuite\ServiceManager;

use KiwiSuite\ServiceManager\Factory\AutowireFactory;
use KiwiSuite\ServiceManager\Factory\LazyServiceDelegatorFactory;

final class ServiceManagerConfigurator
{
    /**
     * @var array
     */
    private $services = [];

    /**
     * @var array
     */
    private $factories = [];

    /**
     * @var array
     */
    private $delegators = [];

    /**
     * @var array
     */
    private $disabledSharing = [];

    /**
     * @var array
     */
    private $lazyServices = [];

    /**
     * @var array
     */
    private $initializers = [];

    /**
     * @var array
     */
    private $subManagers = [];

    public function addFactory(string $name, string $factory = AutowireFactory::class): void
    {
        $this->factories[$name] = $factory;
    }

    /**
     * @return array
     */
    public function getFactories(): array
    {
        return $this->factories;
    }

    public function addDelegator(string $name, array $delegators): void
    {
        if (!\array_key_exists($name, $this->delegators)) {
            $this->delegators[$name] = $delegators;
            return;
        }

        $this->delegators[$name] += $delegators;
    }

    public function getDelegators(): array
    {
        return $this->delegators;
    }

    public function addLazyService(string $name, string $className = null): void
    {
        if ($className === null) {
            $className = $name;
        }

        $this->lazyServices[$name] = $className;
        $this->addDelegator($name, [LazyServiceDelegatorFactory::class]);
    }

    /**
     * @return array
     */
    public function getLazyServices(): array
    {
        return $this->lazyServices;
    }

    public function addInitializer(string $name): void
    {
        $this->initializers[] = $name;
    }

    /**
     * @return array
     */
    public function getInitializers(): array
    {
        return $this->initializers;
    }

    public function disableSharingFor(string $name): void
    {
        $this->disabledSharing[] = $name;
    }

    /**
     * @return array
     */
    public function getDisableSharing(): array
    {
        return $this->disabledSharing;
    }

    public function addSubManager(string $manager, string $factory)
    {
        $this->subManagers[$manager] = $factory;
    }

    /**
     * @return array
     */
    public function getSubManagers(): array
    {
        return $this->subManagers;
    }

    /**
     * @return ServiceManagerConfig
     */
    public function getServiceManagerConfig(): ServiceManagerConfig
    {
        return new ServiceManagerConfig([
            'factories' => $this->getFactories(),
            'initializers' => $this->getInitializers(),
            'delegators' => $this->getDelegators(),
            'subManagers' => $this->getSubManagers(),
            'lazyServices' => $this->getLazyServices(),
            'disabledSharing' => $this->getDisableSharing(),
        ]);
    }
}
