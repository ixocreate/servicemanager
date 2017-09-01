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

use Psr\Container\ContainerInterface;

interface InitializerInterface
{
    /**
     * @param ContainerInterface $container
     * @param $instance
     * @return void
     */
    public function __invoke(ContainerInterface $container, $instance): void;
}
