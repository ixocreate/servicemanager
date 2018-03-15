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
namespace KiwiSuite\ServiceManager\BootstrapItem;

use KiwiSuite\Contract\Application\BootstrapItemInterface;
use KiwiSuite\Contract\Application\ConfiguratorInterface;
use KiwiSuite\ServiceManager\ServiceManagerConfigurator;

final class ServiceManagerBootstrapItem implements BootstrapItemInterface
{

    /**
     * @return ConfiguratorInterface
     */
    public function getConfigurator(): ConfiguratorInterface
    {
        return new ServiceManagerConfigurator();
    }

    /**
     * @return string
     */
    public function getVariableName(): string
    {
        return 'serviceManager';
    }

    /**
     * @return string
     */
    public function getFileName(): string
    {
        return 'servicemanager.php';
    }
}
