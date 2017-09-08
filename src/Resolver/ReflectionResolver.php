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
namespace KiwiSuite\ServiceManager\Resolver;

use KiwiSuite\ServiceManager\Exception\ServiceNotFoundException;
use KiwiSuite\ServiceManager\ServiceManagerConfig;
use Psr\Container\ContainerInterface;

class ReflectionResolver
{
    /**
     * @param ContainerInterface $serviceManager
     * @param string $serviceName
     * @return Resolution
     */
    public function resolveService(ContainerInterface $serviceManager, string $serviceName): Resolution
    {
        if (!\class_exists($serviceName)) {
            throw new ServiceNotFoundException(\sprintf(
                'Class "%s" not found',
                $serviceName
            ), 100);
        }

        return new Resolution($serviceName, $this->getDependencies($serviceManager, $serviceName));
    }

    /**
     * @param ContainerInterface $serviceManager
     * @param string $serviceName
     * @return array
     */
    private function getDependencies(ContainerInterface $serviceManager, string $serviceName): array
    {
        $reflectionClass = new \ReflectionClass($serviceName);

        if (null === ($constructor = $reflectionClass->getConstructor())) {
            return [];
        }

        $reflectionParameters = $constructor->getParameters();

        if (empty($reflectionParameters)) {
            return [];
        }

        //TODO Check if ServiceManagerConfig is available

        $subManagers = $serviceManager->get(ServiceManagerConfig::class)->getSubManagers();

        $dependencies = [];

        foreach ($reflectionParameters as $parameter) {
            if (!$parameter->getClass()) {
                throw new ServiceNotFoundException(
                    \sprintf("Service with the name '%s' can't be found", $parameter->getName()),
                    200
                );
            }

            if ($serviceManager->has($parameter->getClass()->getName())) {
                $dependencies[] = [
                    'serviceName' => $parameter->getClass()->getName(),
                    'subManager' => null,
                ];

                continue;
            }

            foreach (\array_keys($subManagers) as $subManager) {
                if ($serviceManager->get($subManager)->has($parameter->getName())) {
                    $dependencies[] = [
                        'serviceName' => $parameter->getName(),
                        'subManager' => $subManager,
                    ];

                    continue 2;
                }
            }

            if ($serviceManager->has($parameter->getName())) {
                $dependencies[] = [
                    'serviceName' => $parameter->getName(),
                    'subManager' => null,
                ];

                continue;
            }

            throw new ServiceNotFoundException(\sprintf("Service with the name '%s' can't be found", $parameter->getName()), 300);
        }

        return $dependencies;
    }

    /**
     * @param ContainerInterface $serviceManager
     * @param Resolution $resolution
     * @return mixed
     */
    public function createInstance(ContainerInterface $serviceManager, Resolution $resolution)
    {
        $instanceName = $resolution->getServiceName();
        $instanceParameters = [];

        foreach ($resolution->getDependencies() as $dependency) {
            $container = $serviceManager;
            if ($dependency['subManager'] !== null) {
                $container = $serviceManager->get($dependency['subManager']);
            }

            $instanceParameters[] = $container->get($dependency['serviceName']);
        }

        return new $instanceName(...$instanceParameters);
    }
}
