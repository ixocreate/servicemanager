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

use KiwiSuite\ServiceManager\FactoryInterface;
use KiwiSuite\ServiceManager\ServiceManagerInterface;

class ContainerExceptionFactory implements FactoryInterface
{

    /**
     * @param ServiceManagerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return mixed
     * @throws \Exception
     */
    public function __invoke(ServiceManagerInterface $container, $requestedName, array $options = null)
    {
        throw new \Exception("cant create");
    }
}
