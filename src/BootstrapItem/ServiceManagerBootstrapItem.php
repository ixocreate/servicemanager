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
namespace Ixocreate\ServiceManager\BootstrapItem;

use Ixocreate\Contract\Application\BootstrapItemInterface;
use Ixocreate\Contract\Application\ConfiguratorInterface;
use Ixocreate\ServiceManager\ServiceManagerConfigurator;

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
