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
use KiwiSuite\ServiceManager\ServiceManagerInterface;

final class ReflectionResolver implements ResolverInterface
{
    /**
     * @param ServiceManagerInterface $container
     * @param string $serviceName
     * @return Resolution
     */
    public function resolveService(ServiceManagerInterface $container, string $serviceName): Resolution
    {
        if (!\class_exists($serviceName)) {
            throw new ServiceNotFoundException(\sprintf(
                'Class "%s" not found',
                $serviceName
            ), 100);
        }

        return new Resolution($serviceName, $this->getDependencies($container, $serviceName));
    }

    /**
     * @param ServiceManagerInterface $container
     * @param string $serviceName
     * @return array
     */
    private function getDependencies(ServiceManagerInterface $container, string $serviceName): array
    {
        $reflectionClass = new \ReflectionClass($serviceName);

        if (null === ($constructor = $reflectionClass->getConstructor())) {
            return [];
        }

        $reflectionParameters = $constructor->getParameters();

        if (empty($reflectionParameters)) {
            return [];
        }

        $subManagers = $container->getServiceManagerConfig()->getSubManagers();

        $dependencies = [];

        foreach ($reflectionParameters as $parameter) {
            if (!$parameter->getClass()) {
                throw new ServiceNotFoundException(
                    \sprintf("Service with the name '%s' can't be found", $parameter->getName()),
                    200
                );
            }

            if ($container->has($parameter->getClass()->getName())) {
                $dependencies[] = [
                    'serviceName' => $parameter->getClass()->getName(),
                    'subManager' => null,
                ];

                continue;
            }

            foreach (\array_keys($subManagers) as $subManager) {
                if ($container->get($subManager)->has($parameter->getName())) {
                    $dependencies[] = [
                        'serviceName' => $parameter->getName(),
                        'subManager' => $subManager,
                    ];

                    continue 2;
                }
            }

            if ($container->has($parameter->getName())) {
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
}
