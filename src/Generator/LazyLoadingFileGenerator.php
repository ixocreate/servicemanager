<?php
/**
 * @link https://github.com/ixocreate
 * @copyright IXOLIT GmbH
 * @license MIT License
 */

declare(strict_types=1);

namespace Ixocreate\ServiceManager\Generator;

use Ixocreate\ServiceManager\ServiceManager;
use Ixocreate\ServiceManager\ServiceManagerInterface;
use Ixocreate\ServiceManager\SubManagerAwareInterface;

class LazyLoadingFileGenerator
{
    public function generate(ServiceManagerInterface $serviceManager): void
    {
        $this->requestLazyLoadingServices($serviceManager);
        if ($serviceManager->serviceManagerConfig() instanceof SubManagerAwareInterface) {
            foreach (\array_keys($serviceManager->serviceManagerConfig()->getSubManagers()) as $subManager) {
                $this->requestLazyLoadingServices($serviceManager->get($subManager));
            }
        }
    }

    private function requestLazyLoadingServices(ServiceManagerInterface $serviceManager): void
    {
        $serviceManagerConfig = $serviceManager->serviceManagerConfig();
        $serviceManagerSetup = $serviceManager->serviceManagerSetup()->withPersistLazyLoading(true);

        $persistServiceManager = new ServiceManager(
            $serviceManagerConfig,
            $serviceManagerSetup,
            $serviceManager->initialServices()
        );

        foreach (\array_keys($serviceManagerConfig->getLazyServices()) as $serviceName) {
            $persistServiceManager->get($serviceName);
        }
    }
}
