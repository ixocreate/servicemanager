<?php
/**
 * @link https://github.com/ixocreate
 * @copyright IXOLIT GmbH
 * @license MIT License
 */

declare(strict_types=1);

namespace Ixocreate\ServiceManager;

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
