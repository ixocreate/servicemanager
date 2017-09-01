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
namespace KiwiSuite\ServiceManager\SubManager;

use Psr\Container\ContainerInterface;
use Zend\ServiceManager\ServiceManager;

final class PluginManager extends ServiceManager
{
    public function __construct(ContainerInterface $container, array $config = [])
    {
        parent::__construct($config);

        $this->creationContext = $container;
    }
}
