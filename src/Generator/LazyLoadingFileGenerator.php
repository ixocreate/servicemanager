<?php
/**
 * @link https://github.com/ixocreate
 * @copyright IXOCREATE GmbH
 * @license MIT License
 */

declare(strict_types=1);

namespace Ixocreate\ServiceManager\Generator;

use Ixocreate\Contract\ServiceManager\ServiceManagerInterface;
use Ixocreate\ServiceManager\ServiceManager;

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

        $persistServiceManager = new ServiceManager($serviceManagerConfig, $serviceManagerSetup, $serviceManager->initialServices());

        foreach (\array_keys($serviceManagerConfig->getLazyServices()) as $serviceName) {
            $persistServiceManager->get($serviceName);
        }
    }
}
