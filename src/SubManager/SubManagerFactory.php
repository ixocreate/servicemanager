<?php
namespace KiwiSuite\ServiceManager\SubManager;

use KiwiSuite\Contract\ServiceManager\ServiceManagerInterface;
use KiwiSuite\Contract\ServiceManager\SubManager\SubManagerFactoryInterface;
use KiwiSuite\Contract\ServiceManager\SubManager\SubManagerInterface;
use KiwiSuite\ServiceManager\ServiceManagerConfig;

final class SubManagerFactory implements SubManagerFactoryInterface
{

    /**
     * @param ServiceManagerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return SubManagerInterface
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ServiceManagerInterface $container, $requestedName, array $options = null): SubManagerInterface
    {
        /** @var ServiceManagerConfig $serviceManagerConfig */
        $serviceManagerConfig = $container->get($requestedName . '::Config');

        $validation = $serviceManagerConfig->getMetadata('validation');

        return new $requestedName(
            $container,
            $serviceManagerConfig,
            $validation
        );
    }
}
