<?php
/**
 * kiwi-suite/servicemanager (https://github.com/kiwi-suite/servicemanager)
 *
 * @package kiwi-suite/servicemanager
 * @link https://github.com/kiwi-suite/servicemanager
 * @copyright Copyright (c) 2010 - 2018 kiwi suite GmbH
 * @license MIT License
 */

declare(strict_types=1);
namespace KiwiSuite\ServiceManager\Factory;

use KiwiSuite\Contract\ServiceManager\AutowireFactoryInterface;
use KiwiSuite\Contract\ServiceManager\ServiceManagerInterface;

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
        return $container->getFactoryResolver()->getFactory($requestedName)($container, $requestedName, $options);
    }
}
