<?php
/**
 * @link https://github.com/ixocreate
 * @copyright IXOLIT GmbH
 * @license MIT License
 */

declare(strict_types=1);

namespace Ixocreate\ServiceManager\Factory;

use Ixocreate\ServiceManager\ServiceManagerInterface;

final class AutowireFactory implements AutowireFactoryInterface
{
    /**
     * @param ServiceManagerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return mixed
     */
    public function __invoke(ServiceManagerInterface $container, $requestedName, array $options = null)
    {
        return $container->getFactoryResolver()->getFactory($requestedName)($container, $requestedName, $options);
    }
}
