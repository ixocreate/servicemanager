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
use KiwiSuite\ServiceManager\Resolver\ResolverInterface;
use ProxyManager\Factory\LazyLoadingValueHolderFactory;
use KiwiSuite\ServiceManager\Factory\LazyLoadingValueHolderFactory as KiwiLazyLoadingValueHolderFactory;

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
     * @var ServiceManagerSetup
     */
    private $serviceManagerSetup;

    /**
     * @var ResolverInterface
     */
    private $resolver;

    /**
     * @param ServiceManagerConfig $serviceManagerConfig
     * @param ServiceManagerSetup $serviceManagerSetup
     */
    public function __construct(ServiceManagerConfig $serviceManagerConfig, ServiceManagerSetup $serviceManagerSetup)
    {
        $this->serviceManagerConfig = $serviceManagerConfig;

        $factories = $serviceManagerConfig->getFactories();
        $factories[LazyLoadingValueHolderFactory::class] = KiwiLazyLoadingValueHolderFactory::class;

        $factories = \array_merge($factories, $serviceManagerConfig->getSubManagers());

        $this->serviceManager = new OriginalServiceManager($this, [
            'factories' => $factories,
            'delegators' => $serviceManagerConfig->getDelegators(),
            'shared' => \array_fill_keys($serviceManagerConfig->getDisabledSharing(), false),
            'initializers' => $serviceManagerConfig->getInitializers(),
            'shared_by_default' => true,
        ]);

        $this->serviceManagerSetup = $serviceManagerSetup;
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

    /**
     * @return ServiceManagerConfig
     */
    public function getServiceManagerConfig(): ServiceManagerConfig
    {
        return $this->serviceManagerConfig;
    }

    /**
     * @return ServiceManagerSetup
     */
    public function getServiceManagerSetup(): ServiceManagerSetup
    {
        return $this->serviceManagerSetup;
    }

    /**
     * @return ResolverInterface
     */
    public function getResolver(): ResolverInterface
    {
        if ($this->resolver === null) {
            $resolverName = $this->getServiceManagerSetup()->getAutowireResolver();

            $this->resolver = new $resolverName();
        }

        return $this->resolver;
    }
}
