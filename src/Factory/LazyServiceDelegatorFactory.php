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
namespace KiwiSuite\ServiceManager\Factory;

use KiwiSuite\ServiceManager\DelegatorFactoryInterface;
use KiwiSuite\ServiceManager\Exception\ServiceNotFoundException;
use KiwiSuite\ServiceManager\ServiceManagerConfig;
use ProxyManager\Proxy\LazyLoadingInterface;
use Psr\Container\ContainerInterface;

final class LazyServiceDelegatorFactory implements DelegatorFactoryInterface
{
    public function __invoke(ContainerInterface $container, $name, callable $callback, array $options = null)
    {
        /** @var ServiceManagerConfig $serviceManagerConfig */
        $serviceManagerConfig = $container->getServiceManagerConfig();

        /** @var \ProxyManager\Factory\LazyLoadingValueHolderFactory $proxyFactory */
        $proxyFactory = $container->get(\ProxyManager\Factory\LazyLoadingValueHolderFactory::class);

        $initializer = function (&$wrappedInstance, LazyLoadingInterface $proxy) use ($callback) {
            $proxy->setProxyInitializer(null);
            $wrappedInstance = $callback();

            return true;
        };

        $lazyServices = $serviceManagerConfig->getLazyServices();

        if (!isset($lazyServices[$name])) {
            throw new ServiceNotFoundException(\sprintf("LazyService with name '%s' not found", $name));
        }

        return $proxyFactory->createProxy($lazyServices[$name], $initializer);
    }
}
