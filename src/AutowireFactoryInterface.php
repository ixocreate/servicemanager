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
namespace KiwiSuite\ServiceManager;

use KiwiSuite\ServiceManager\Resolver\Resolution;

interface AutowireFactoryInterface extends FactoryInterface
{
    /**
     * @param ServiceManagerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return mixed
     */
    public function getResolution(ServiceManagerInterface $container, string $requestedName, array $options = null): Resolution;
}
