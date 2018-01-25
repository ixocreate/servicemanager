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
namespace KiwiSuiteMisc\ServiceManager;

use KiwiSuite\ServiceManager\ServiceManagerConfig;
use KiwiSuite\ServiceManager\ServiceManagerInterface;
use KiwiSuite\ServiceManager\SubManager\SubManager;
use KiwiSuite\ServiceManager\SubManager\SubManagerFactoryInterface;
use KiwiSuite\ServiceManager\SubManager\SubManagerInterface;

class SubManagerFactory implements SubManagerFactoryInterface
{

    /**
     * @param ServiceManagerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return SubManagerInterface
     */
    public function __invoke(ServiceManagerInterface $container, $requestedName, array $options = null): SubManagerInterface
    {
        return new SubManager(
            $container,
            new ServiceManagerConfig([
                'test1' => DateTimeFactory::class,
                'value3' => DateTimeFactory::class,
                OwnDateTime::class => DateTimeFactory::class,
            ]),
            \DateTimeInterface::class
        );
    }
}
