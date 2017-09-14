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
use KiwiSuite\ServiceManager\Generator\AutowireCacheGenerator;
use KiwiSuite\ServiceManager\ServiceManagerInterface;

final class InMemoryResolver implements ResolverInterface
{
    /**
     * @var Resolution[]
     */
    private $resolutions = null;

    /**
     * @param ServiceManagerInterface $container
     * @param string $serviceName
     * @return Resolution
     */
    public function resolveService(ServiceManagerInterface $container, string $serviceName): Resolution
    {
        if ($this->resolutions === null) {
            $generator = new AutowireCacheGenerator();
            $this->resolutions = $generator->generate($container);
        }

        if (!\array_key_exists($serviceName, $this->resolutions)) {
            throw new ServiceNotFoundException(\sprintf("Service with name '%s' can't be resolved because of a missing autowire resolution", $serviceName));
        }

        return $this->resolutions[$serviceName];
    }
}
