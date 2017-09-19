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

use KiwiSuite\ServiceManager\ConfigProviderInterface;
use KiwiSuite\ServiceManager\ServiceManagerConfig;
use KiwiSuite\ServiceManager\ServiceManagerConfigurator;

class ConfigProvider implements ConfigProviderInterface
{

    /**
     * ConfigProviderInterface constructor.
     */
    public function __construct()
    {
    }

    /**
     * @return ServiceManagerConfig
     */
    public function getServiceManagerConfig(): ServiceManagerConfig
    {
        $serviceManagerConfigurator = new ServiceManagerConfigurator();
        $serviceManagerConfigurator->addFactory("dateTimeFromConfigProvider", DateTimeFactory::class);

        return $serviceManagerConfigurator->getServiceManagerConfig();
    }
}
