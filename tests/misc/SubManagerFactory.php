<?php
/**
 * @link https://github.com/ixocreate
 * @copyright IXOLIT GmbH
 * @license MIT License
 */

declare(strict_types=1);

namespace Ixocreate\Misc\ServiceManager;

use Ixocreate\ServiceManager\ServiceManagerConfigInterface;
use Ixocreate\ServiceManager\ServiceManagerInterface;
use Ixocreate\ServiceManager\SubManager\SubManager;
use Ixocreate\ServiceManager\SubManager\SubManagerFactoryInterface;
use Ixocreate\ServiceManager\SubManager\SubManagerInterface;

class SubManagerFactory implements SubManagerFactoryInterface
{
    /**
     * @param ServiceManagerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return SubManagerInterface
     */
    public function __invoke(
        ServiceManagerInterface $container,
        $requestedName,
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
            public function getSubManagers(): array
            {
                return [];
            }

            /**
             * @return array
             */
            public function getConfig(): array
            {
                $factories = \array_merge($this->getFactories(), $this->getSubManagers());
                return [
                    'factories' => $factories,
                    'delegators' => $this->getDelegators(),
                    'initializers' => $this->getInitializers(),
                    'shared_by_default' => true,
                ];
            }

            /**
             * @return array
             */
            public function getNamedServices(): array
            {
                return [];
            }

            /**
             * @param string|null $name
             * @param null $default
             * @return mixed
             */
            public function getMetadata(string $name = null, $default = null)
            {
                return $default;
            }
        };

        return new SubManager(
            $container,
            $serviceManagerConfig,
            \DateTimeInterface::class
        );
    }
}
