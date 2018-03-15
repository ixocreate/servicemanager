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
namespace KiwiSuite\ServiceManager\SubManager;

use KiwiSuite\Contract\ServiceManager\ServiceManagerInterface;
use KiwiSuite\Contract\ServiceManager\SubManager\SubManagerFactoryInterface;
use KiwiSuite\Contract\ServiceManager\SubManager\SubManagerInterface;
use KiwiSuite\ServiceManager\ServiceManagerConfig;

final class SubManagerFactory implements SubManagerFactoryInterface
{

    /**
     * @param ServiceManagerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     * @return SubManagerInterface
     */
    public function __invoke(ServiceManagerInterface $container, $requestedName, array $options = null): SubManagerInterface
    {
        /** @var ServiceManagerConfig $serviceManagerConfig */
        $serviceManagerConfig = $container->get($requestedName . '::Config');

        $validation = $serviceManagerConfig->getMetadata('validation');

        return new $requestedName(
            $container,
            $serviceManagerConfig,
            $validation
        );
    }
}
