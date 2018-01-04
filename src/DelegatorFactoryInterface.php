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
namespace KiwiSuite\ServiceManager;

interface DelegatorFactoryInterface
{
    /**
     * @param ServiceManagerInterface $container
     * @param $name
     * @param callable $callback
     * @param array|null $options
     * @return mixed
     */
    public function __invoke(ServiceManagerInterface $container, $name, callable $callback, array $options = null);
}
