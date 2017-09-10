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
use ProxyManager\Configuration;
use ProxyManager\GeneratorStrategy\EvaluatingGeneratorStrategy;
use Psr\Container\ContainerInterface;

class LazyLoadingValueHolderFactory implements FactoryInterface
{

    /**
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return mixed
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $proxyConfiguration = new Configuration();
        $proxyConfiguration->setGeneratorStrategy(new EvaluatingGeneratorStrategy());

        \spl_autoload_register($proxyConfiguration->getProxyAutoloader());

        return new \ProxyManager\Factory\LazyLoadingValueHolderFactory($proxyConfiguration);
    }
}
