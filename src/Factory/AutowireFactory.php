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

use KiwiSuite\ServiceManager\AutowireFactoryInterface;
use KiwiSuite\ServiceManager\Resolver\ReflectionResolver;
use KiwiSuite\ServiceManager\Resolver\Resolution;
use KiwiSuite\ServiceManager\ServiceManager;
use KiwiSuite\ServiceManager\ServiceManagerInterface;

final class AutowireFactory implements AutowireFactoryInterface
{

    /**
     * @param ServiceManagerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return mixed
     */
    public function __invoke(ServiceManagerInterface $container, $requestedName, array $options = null)
    {
        return $this->getResolution($container, $requestedName, $options)->createInstance($container);
    }

    /**
     * @param ServiceManagerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return mixed
     */
    public function getResolution(ServiceManagerInterface $container, string $requestedName, array $options = null): Resolution
    {
        if (!($container instanceof ServiceManager)) {
            //TODO Exception
        }



        $resolver = new ReflectionResolver();
        return $resolver->resolveService($container, $requestedName);
    }
}
