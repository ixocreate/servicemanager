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
use KiwiSuite\ServiceManager\ServiceManagerSetup;

final class CacheResolver implements ResolverInterface
{
    /**
     * @var Resolution[]
     */
    private $resolutions;

    /**
     * @param ServiceManagerInterface $container
     * @param string $serviceName
     * @return Resolution
     */
    public function resolveService(ServiceManagerInterface $container, string $serviceName): Resolution
    {
        if ($this->resolutions === null) {
            $this->enforceDirectory($container->getServiceManagerSetup());
            $this->fillCache($container->getServiceManagerSetup());
        }

        if (!\array_key_exists($serviceName, $this->resolutions)) {
            throw new ServiceNotFoundException(\sprintf("Service with name '%s' can't be resolved because of a missing autowire resolution", $serviceName));
        }

        return $this->resolutions[$serviceName];
    }

    private function enforceDirectory(ServiceManagerSetup $serviceManagerSetup): void
    {
        if (!\file_exists($serviceManagerSetup->getAutowireLocation())) {
            \mkdir($serviceManagerSetup->getAutowireLocation(), 0777, true);
        }
    }

    private function fillCache(ServiceManagerSetup $serviceManagerSetup): void
    {
        if (!\file_exists($serviceManagerSetup->getAutowireCacheFileLocation())) {
            return;
        }

        $serialized = \file_get_contents($serviceManagerSetup->getAutowireCacheFileLocation());

        $unserialized = \unserialize($serialized);
        if ($unserialized === false || !\is_array($unserialized)) {
            return;
        }

        $this->resolutions = $unserialized;
    }
}
