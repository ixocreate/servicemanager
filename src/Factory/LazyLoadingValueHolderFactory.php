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

use KiwiSuite\ServiceManager\FactoryInterface;
use KiwiSuite\ServiceManager\ServiceManagerInterface;
use ProxyManager\Configuration;
use ProxyManager\FileLocator\FileLocator;
use ProxyManager\GeneratorStrategy\EvaluatingGeneratorStrategy;
use ProxyManager\GeneratorStrategy\FileWriterGeneratorStrategy;

class LazyLoadingValueHolderFactory implements FactoryInterface
{

    /**
     * @param ServiceManagerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return mixed
     */
    public function __invoke(ServiceManagerInterface $container, $requestedName, array $options = null)
    {
        $proxyConfiguration = new Configuration();
        if ($container->getServiceManagerSetup()->isPersistLazyLoading()) {
            if (!\file_exists($container->getServiceManagerSetup()->getLazyLoadingLocation())) {
                @\mkdir($container->getServiceManagerSetup()->getLazyLoadingLocation(), 0777, true);
            }
            $proxyConfiguration->setGeneratorStrategy(new FileWriterGeneratorStrategy(
                new FileLocator($container->getServiceManagerSetup()->getLazyLoadingLocation())
            ));
            $proxyConfiguration->setProxiesTargetDir($container->getServiceManagerSetup()->getLazyLoadingLocation());
        } else {
            $proxyConfiguration->setGeneratorStrategy(new EvaluatingGeneratorStrategy());
        }

        \spl_autoload_register($proxyConfiguration->getProxyAutoloader());

        return new \ProxyManager\Factory\LazyLoadingValueHolderFactory($proxyConfiguration);
    }
}
