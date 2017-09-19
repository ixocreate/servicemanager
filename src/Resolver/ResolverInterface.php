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

use KiwiSuite\ServiceManager\ServiceManagerInterface;

interface ResolverInterface
{
    /**
     * @param ServiceManagerInterface $container
     * @param string $serviceName
     * @return Resolution
     */
    public function resolveService(ServiceManagerInterface $container, string $serviceName): Resolution;
}
