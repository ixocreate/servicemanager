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

use KiwiSuite\ServiceManager\Exception\InvalidArgumentException;
use KiwiSuite\ServiceManager\SubManager\SubManagerFactoryInterface;
use Zend\ServiceManager\Proxy\LazyServiceFactory;

class ServiceManagerConfig implements \Serializable
{
    /**
     * @var array
     */
    private $config;

    /**
     * ServiceManagerConfig constructor.
     * @param array $factories
     * @param array $subManagers
     * @param array $delegators
     * @param array $lazyServices
     * @param array $disabledSharing
     * @param array $initializers
     */
    public function __construct(
        array $factories = [],
        array $subManagers = [],
        array $delegators = [],
        array $lazyServices = [],
        array $disabledSharing = [],
        array $initializers = []
    ) {
        $config['factories'] = $factories;
        $this->validateFactories($factories);

        $config['subManagers'] = $subManagers;
        $this->validateSubManagers($subManagers);

        $config['delegators'] = $delegators;
        $this->validateDelegators($delegators);

        $config['lazyServices'] = $lazyServices;
        $this->validateLazyServices($lazyServices);

        $config['disabledSharing'] = $disabledSharing;
        $this->validateDisabledSharing($disabledSharing);

        $config['initializers'] = $initializers;
        $this->validateInitializers($initializers);

        $this->config = $config;
    }

    /**
     * @param array $config
     */
    private function validateFactories(array $config): void
    {
        foreach ($config as $factoryName => $factory) {
            if (!\is_string($factory)) {
                throw new InvalidArgumentException(\sprintf("'%s' is not a valid factory", \var_export($factoryName, true)));
            }
            $classImplements = @\class_implements($factory);
            if (!\is_array($classImplements)) {
                throw new InvalidArgumentException(\sprintf("Factory '%s' can't be loaded", $factory));
            }
            if (!\in_array(FactoryInterface::class, $classImplements)) {
                throw new InvalidArgumentException(\sprintf("'%s' doesn't implement '%s'", $factoryName, FactoryInterface::class));
            }
        }
    }

    /**
     * @param array $config
     */
    private function validateDisabledSharing(array $config): void
    {
        foreach ($config as $service) {
            if (!\is_string($service)) {
                throw new InvalidArgumentException(\sprintf("'%s' is not a valid factory", \var_export($service, true)));
            }
        }
    }

    /**
     * @param array $config
     */
    private function validateDelegators(array $config): void
    {
        foreach ($config as $name => $delegators) {
            if (!\is_array($delegators)) {
                throw new InvalidArgumentException(\sprintf("'%s' is not a valid delegator definition", $name));
            }

            foreach ($delegators as $delegator) {
                if (!\is_string($delegator)) {
                    throw new InvalidArgumentException(\sprintf("'%s' is not a valid delegator", \var_export($delegator, true)));
                }

                if ($delegator === LazyServiceFactory::class) {
                    continue;
                }

                $classImplements = @\class_implements($delegator);
                if (!\is_array($classImplements)) {
                    throw new InvalidArgumentException(\sprintf("Delegator '%s' can't be loaded", $delegator));
                }
                if (!\in_array(DelegatorFactoryInterface::class, $classImplements)) {
                    throw new InvalidArgumentException(\sprintf("'%s' doesn't implement '%s'", $delegator, DelegatorFactoryInterface::class));
                }
            }
        }
    }

    /**
     * @param array $config
     */
    private function validateInitializers(array $config): void
    {
        foreach ($config as $initializers) {
            if (!\is_string($initializers)) {
                throw new InvalidArgumentException(\sprintf("'%s' is not a valid factory", \var_export($initializers, true)));
            }
            $classImplements = @\class_implements($initializers);
            if (!\is_array($classImplements)) {
                throw new InvalidArgumentException(\sprintf("Factory '%s' can't be loaded", $initializers));
            }
            if (!\in_array(InitializerInterface::class, $classImplements)) {
                throw new InvalidArgumentException(\sprintf("'%s' doesn't implement '%s'", $initializers, InitializerInterface::class));
            }
        }
    }

    /**
     * @param array $config
     */
    private function validateLazyServices(array $config): void
    {
        foreach ($config as $lazyName => $lazyClass) {
            if (!\class_exists($lazyClass)) {
                throw new InvalidArgumentException(\sprintf("'%s' is not a valid class", $lazyClass));
            }
        }
    }

    /**
     * @param array $config
     */
    private function validateSubManagers(array $config): void
    {
        foreach ($config as $factoryName => $factory) {
            if (!\is_string($factory)) {
                throw new InvalidArgumentException(\sprintf("'%s' is not a valid factory", \var_export($factory, true)));
            }
            $classImplements = @\class_implements($factory);
            if (!\is_array($classImplements)) {
                throw new InvalidArgumentException(\sprintf("Factory '%s' can't be loaded", $factory));
            }
            if (!\in_array(SubManagerFactoryInterface::class, $classImplements)) {
                throw new InvalidArgumentException(\sprintf("'%s' doesn't implement '%s'", $factoryName, SubManagerFactoryInterface::class));
            }
        }
    }

    /**
     * @param array $config
     */
    final protected function setInternalConfig(array $config): void {
        $this->config = $config;
    }

    /**
     * @return array
     */
    final protected function getInternalConfig(): array {
        return $this->config;
    }

    /**
     * @return array
     */
    final public function getFactories(): array
    {
        return $this->config['factories'];
    }

    /**
     * @return array
     */
    final public function getDisabledSharing(): array
    {
        return $this->config['disabledSharing'];
    }

    /**
     * @return array
     */
    final public function getDelegators(): array
    {
        return $this->config['delegators'];
    }

    /**
     * @return array
     */
    final public function getInitializers(): array
    {
        return $this->config['initializers'];
    }

    /**
     * @return array
     */
    final public function getLazyServices(): array
    {
        return $this->config['lazyServices'];
    }

    /**
     * @return array
     */
    final public function getSubManagers(): array
    {
        return $this->config['subManagers'];
    }

    /**
     * @return string
     */
    public function serialize()
    {
        return \serialize($this->config);
    }

    /**
     * @param string $serialized
     */
    public function unserialize($serialized)
    {
        $this->config = \unserialize($serialized);
    }

    /**
     * @return array
     */
    final public function getConfig(): array
    {
        $factories = \array_merge($this->config['factories'], $this->config['subManagers']);

        return [
            'factories' => $factories,
            'delegators' => $this->getDelegators(),
            'shared' => \array_fill_keys($this->getDisabledSharing(), false),
            'initializers' => $this->getInitializers(),
            'shared_by_default' => true,
        ];
    }
}
