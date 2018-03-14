<?php
/**
 * kiwi-suite/servicemanager (https://github.com/kiwi-suite/servicemanager)
 *
 * @package kiwi-suite/servicemanager
 * @see https://github.com/kiwi-suite/servicemanager
 * @copyright Copyright (c) 2010 - 2018 kiwi suite GmbH
 * @license MIT License
 */

declare(strict_types=1);
namespace KiwiSuite\ServiceManager\Generator;

use KiwiSuite\Contract\ServiceManager\ServiceManagerInterface;
use KiwiSuite\ServiceManager\ServiceManager;

class LazyLoadingFileGenerator
{
    public function generate(ServiceManagerInterface $serviceManager): void
    {
        $this->requestLazyLoadingServices($serviceManager);
        foreach (\array_keys($serviceManager->getServiceManagerConfig()->getSubManagers()) as $subManager) {
            $this->requestLazyLoadingServices($serviceManager->get($subManager));
        }
    }

    private function requestLazyLoadingServices(ServiceManagerInterface $serviceManager): void
    {
        $serviceManagerConfig = $serviceManager->getServiceManagerConfig();
        $serviceManagerSetup = $serviceManager->getServiceManagerSetup()->withPersistLazyLoading(true);

        $persistServiceManager = new ServiceManager($serviceManagerConfig, $serviceManagerSetup);

        foreach (\array_keys($serviceManagerConfig->getLazyServices()) as $serviceName) {
            $persistServiceManager->get($serviceName);
        }
    }
}
