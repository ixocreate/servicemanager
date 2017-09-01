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

use KiwiSuite\ServiceManager\Exception\ServiceNotCreatedException;
use KiwiSuite\ServiceManager\Exception\ServiceNotFoundException;
use Psr\Container\ContainerInterface;

interface ServiceManagerInterface extends ContainerInterface
{
    /**
     * @param string $id
     * @param array|null $options
     * @throws ServiceNotCreatedException
     * @throws ServiceNotFoundException
     * @return mixed
     */
    public function build(string $id, array $options = null);
}
