<?php
/**
 * @link https://github.com/ixocreate
 * @copyright IXOCREATE GmbH
 * @license MIT License
 */

declare(strict_types=1);

namespace IxocreateMisc\ServiceManager;

use Ixocreate\Contract\ServiceManager\ServiceManagerInterface;
use Ixocreate\Contract\ServiceManager\SubManager\SubManagerFactoryInterface;
use Ixocreate\Contract\ServiceManager\SubManager\SubManagerInterface;
use Ixocreate\ServiceManager\ServiceManagerConfig;
use Ixocreate\ServiceManager\SubManager\SubManager;

class SubManagerFactory implements SubManagerFactoryInterface
{
    /**
     * @param ServiceManagerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return SubManagerInterface
     */
    public function __invoke(ServiceManagerInterface $container, $requestedName, array $options = null): SubManagerInterface
    {
        return new SubManager(
            $container,
            new ServiceManagerConfig([
                'test1' => DateTimeFactory::class,
                'value3' => DateTimeFactory::class,
                OwnDateTime::class => DateTimeFactory::class,
            ]),
            \DateTimeInterface::class
        );
    }
}
