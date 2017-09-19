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
namespace KiwiSuite\ServiceManager\Generator;

use KiwiSuite\ServiceManager\AutowireFactoryInterface;
use KiwiSuite\ServiceManager\Resolver\ReflectionResolver;
use KiwiSuite\ServiceManager\ServiceManager;
use KiwiSuite\ServiceManager\ServiceManagerInterface;

class AutowireCacheGenerator
{
    public function generate(ServiceManagerInterface $serviceManager): array
    {
        $resolutions = $this->requestAutowireFactories($serviceManager);
        foreach (\array_keys($serviceManager->getServiceManagerConfig()->getSubManagers()) as $subManager) {
            $resolutions = \array_merge($resolutions, $this->requestAutowireFactories($serviceManager->get($subManager)));
        }

        return $resolutions;
    }

    public function write(ServiceManagerInterface $serviceManager, array $resolutions): void
    {
        if (!\file_exists($serviceManager->getServiceManagerSetup()->getAutowireLocation())) {
            \mkdir($serviceManager->getServiceManagerSetup()->getAutowireLocation(), 0777, true);
        }

        \file_put_contents($serviceManager->getServiceManagerSetup()->getAutowireCacheFileLocation(), \serialize($resolutions));
    }

    private function requestAutowireFactories(ServiceManagerInterface $serviceManager): array
    {
        $resolutions = [];

        $serviceManagerConfig = $serviceManager->getServiceManagerConfig();
        $serviceManagerSetup = $serviceManager->getServiceManagerSetup()->withAutowireResolver(ReflectionResolver::class);

        $persistServiceManager = new ServiceManager($serviceManagerConfig, $serviceManagerSetup);

        foreach ($serviceManagerConfig->getFactories() as $serviceName => $factory) {
            $implements = \class_implements($factory);
            if (!\in_array(AutowireFactoryInterface::class, $implements)) {
                continue;
            }
            /** @var AutowireFactoryInterface $factoryInstance */
            $factoryInstance = new $factory();
            $resolutions[$serviceName] = $factoryInstance->getResolution($persistServiceManager, $serviceName);
        }

        return $resolutions;
    }
}
