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
namespace KiwiSuiteMisc\ServiceManager;

use KiwiSuite\ServiceManager\ServiceManagerConfig;
use KiwiSuite\ServiceManager\SubManager\SubManager;
use KiwiSuite\ServiceManager\SubManager\SubManagerFactoryInterface;
use KiwiSuite\ServiceManager\SubManager\SubManagerInterface;
use Psr\Container\ContainerInterface;

class SubManagerFactory implements SubManagerFactoryInterface
{

    /**
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return SubManagerInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): SubManagerInterface
    {
        return new SubManager(
            $container,
            new ServiceManagerConfig([
                'factories' => [
                    'test1' => DateTimeFactory::class,
                ]
            ]),
            \DateTimeInterface::class
        );
    }
}
