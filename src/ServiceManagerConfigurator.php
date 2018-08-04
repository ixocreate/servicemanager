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
namespace KiwiSuite\ServiceManager;

use KiwiSuite\Contract\Application\ServiceRegistryInterface;
use KiwiSuite\Contract\ServiceManager\SubManager\SubManagerFactoryInterface;
use KiwiSuite\ServiceManager\Exception\InvalidArgumentException;
use KiwiSuite\ServiceManager\SubManager\SubManagerFactory;

final class ServiceManagerConfigurator extends AbstractServiceManagerConfigurator
{
    /**
     * @var array
     */
    private $subManagers = [];


    /**
     * @param string $manager
     * @param string $factory
     */
    public function addSubManager(string $manager, string $factory = SubManagerFactory::class): void
    {
        if (!\class_exists($factory)) {
            throw new InvalidArgumentException(\sprintf("Factory '%s' can't be loaded", $factory));
        }

        $classImplements = @\class_implements($factory);
        if (!\is_array($classImplements)) {
            throw new InvalidArgumentException(\sprintf("Factory '%s' can't be loaded", $factory));
        }
        if (!\in_array(SubManagerFactoryInterface::class, $classImplements)) {
            throw new InvalidArgumentException(\sprintf("'%s' doesn't implement '%s'", $factory, SubManagerFactoryInterface::class));
        }

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
     * @param ServiceRegistryInterface $serviceRegistry
     * @return void
     */
    public function registerService(ServiceRegistryInterface $serviceRegistry): void
    {
        $serviceRegistry->add(ServiceManagerConfig::class, $this->getServiceManagerConfig());
    }
}
