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

use KiwiSuite\ServiceManager\Exception\ServiceNotCreatedException;
use KiwiSuite\ServiceManager\Exception\ServiceNotFoundException;
use ProxyManager\Factory\LazyLoadingValueHolderFactory;

final class ServiceManager implements ServiceManagerInterface
{
    /**
     * @var OriginalServiceManager
     */
    private $serviceManager;

    /**
     * @var ServiceManagerConfig
     */
    private $serviceManagerConfig;

    /**
     * @param ServiceManagerConfig $serviceManagerConfig
     */
    public function __construct(ServiceManagerConfig $serviceManagerConfig)
    {
        $this->serviceManagerConfig = $serviceManagerConfig;

        $services = [];
        $services[ServiceManagerConfig::class] = $serviceManagerConfig;
        $services[ServiceManager::class] = $this;

        $factories = $serviceManagerConfig->getFactories();
        $factories[LazyLoadingValueHolderFactory::class] = \KiwiSuite\ServiceManager\Factory\LazyLoadingValueHolderFactory::class;

        $factories = \array_merge($factories, $serviceManagerConfig->getSubManagers());

        $this->serviceManager = new OriginalServiceManager($this, [
            'services' => $services,
            'factories' => $factories,
            'delegators' => $serviceManagerConfig->getDelegators(),
            'shared' => \array_fill_keys($serviceManagerConfig->getDisabledSharing(), false),
            'initializers' => $serviceManagerConfig->getInitializers(),
            'shared_by_default' => true,
        ]);
    }

    /**
     * @param string $id
     * @throws ServiceNotCreatedException
     * @throws ServiceNotFoundException
     * @return mixed
     */
    public function get($id)
    {
        try {
            return $this->serviceManager->get($id);
        } catch (\Zend\ServiceManager\Exception\ServiceNotFoundException $exception) {
            throw new ServiceNotFoundException($exception->getMessage(), $exception->getCode(), $exception);
        } catch (\Zend\ServiceManager\Exception\ServiceNotCreatedException $exception) {
            throw new ServiceNotCreatedException($exception->getMessage(), $exception->getCode(), $exception);
        }
    }

    /**
     * @param string $id
     * @return bool
     */
    public function has($id): bool
    {
        return $this->serviceManager->has($id);
    }

    /**
     * @param string $id
     * @param array|null $options
     * @throws ServiceNotCreatedException
     * @throws ServiceNotFoundException
     * @return mixed
     */
    public function build(string $id, array $options = null)
    {
        try {
            return $this->serviceManager->build($id, $options);
        } catch (\Zend\ServiceManager\Exception\ServiceNotFoundException $exception) {
            throw new ServiceNotFoundException($exception->getMessage(), $exception->getCode(), $exception);
        } catch (\Zend\ServiceManager\Exception\ServiceNotCreatedException $exception) {
            throw new ServiceNotCreatedException($exception->getMessage(), $exception->getCode(), $exception);
        }
    }
}
