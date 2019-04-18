<?php
/**
 * @link https://github.com/ixocreate
 * @copyright IXOCREATE GmbH
 * @license MIT License
 */

declare(strict_types=1);

namespace Ixocreate\ServiceManager\SubManager;

use Ixocreate\ServiceManager\ServiceManagerConfigInterface;
use Ixocreate\ServiceManager\ServiceManagerInterface;

final class SubManagerFactory implements SubManagerFactoryInterface
{
    /**
     * @param ServiceManagerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @throws \Psr\Container\NotFoundExceptionInterface
     * @throws \Psr\Container\ContainerExceptionInterface
     * @return SubManagerInterface
     */
    public function __invoke(
        ServiceManagerInterface $container,
        $requestedName,
        array $options = null
    ): SubManagerInterface {
        /** @var ServiceManagerConfigInterface $serviceManagerConfig */
        $serviceManagerConfig = $container->get($requestedName . '::Config');

        $validation = $serviceManagerConfig->getMetadata('validation');

        return new $requestedName(
            $container,
            $serviceManagerConfig,
            $validation
        );
    }
}
