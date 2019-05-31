<?php
/**
 * @link https://github.com/ixocreate
 * @copyright IXOLIT GmbH
 * @license MIT License
 */

declare(strict_types=1);

namespace Ixocreate\ServiceManager\SubManager;

use Ixocreate\ServiceManager\ServiceManagerInterface;

interface SubManagerFactoryInterface
{
    /**
     * @param ServiceManagerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return SubManagerInterface
     */
    public function __invoke(ServiceManagerInterface $container, string $requestedName, array $options = null): SubManagerInterface;
}
