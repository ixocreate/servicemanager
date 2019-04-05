<?php
/**
 * @link https://github.com/ixocreate
 * @copyright IXOCREATE GmbH
 * @license MIT License
 */

declare(strict_types=1);

namespace Ixocreate\ServiceManager;

use Ixocreate\Contract\Application\ServiceRegistryInterface;
use Ixocreate\Contract\ServiceManager\SubManager\SubManagerFactoryInterface;
use Ixocreate\Contract\ServiceManager\SubManager\SubManagerInterface;
use Ixocreate\ServiceManager\Exception\InvalidArgumentException;
use Ixocreate\ServiceManager\SubManager\SubManagerFactory;

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
        if (!\is_subclass_of($manager, SubManagerInterface::class, true)) {
            throw new InvalidArgumentException(\sprintf("'%s' doesn't implement '%s'", $manager, SubManagerInterface::class));
        }

        if (!\class_exists($factory)) {
            throw new InvalidArgumentException(\sprintf("Factory '%s' can't be loaded", $factory));
        }

        if (!\is_subclass_of($factory, SubManagerFactoryInterface::class)) {
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
