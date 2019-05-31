<?php
/**
 * @link https://github.com/ixocreate
 * @copyright IXOLIT GmbH
 * @license MIT License
 */

declare(strict_types=1);

namespace Ixocreate\Misc\ServiceManager\SubManager;

use Ixocreate\Misc\ServiceManager\DateTimeFactory;
use Ixocreate\Misc\ServiceManager\OwnDateTime;
use Ixocreate\ServiceManager\ServiceManagerConfigInterface;
use Ixocreate\ServiceManager\ServiceManagerInterface;
use Ixocreate\ServiceManager\SubManager\SubManagerFactoryInterface;
use Ixocreate\ServiceManager\SubManager\SubManagerInterface;

class DateTimeManagerFactory implements SubManagerFactoryInterface
{
    /**
     * @param ServiceManagerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return SubManagerInterface
     */
    public function __invoke(
        ServiceManagerInterface $container,
        string $requestedName,
        array $options = null
    ): SubManagerInterface {
        $serviceManagerConfig = new class() implements ServiceManagerConfigInterface {
            /**
             * @return array
             */
            public function getFactories(): array
            {
                return [
                    'test1' => DateTimeFactory::class,
                    'value3' => DateTimeFactory::class,
                    OwnDateTime::class => DateTimeFactory::class,
                ];
            }

            /**
             * @return array
             */
            public function getDelegators(): array
            {
                return [];
            }

            /**
             * @return array
             */
            public function getInitializers(): array
            {
                return [];
            }

            /**
             * @return array
             */
            public function getLazyServices(): array
            {
                return [];
            }

            /**
             * @return array
             */
            public function getNamedServices(): array
            {
                return [];
            }
        };

        return new DateTimeManager(
            $container,
            $serviceManagerConfig
        );
    }
}
