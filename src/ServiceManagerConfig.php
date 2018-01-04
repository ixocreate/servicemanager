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

class ServiceManagerConfig implements \Serializable
{
    /**
     * @var array
     */
    private $config;

    private $types = ['configProviders', 'factories', 'disabledSharing', 'delegators', 'initializers', 'lazyServices', 'subManagers'];

    /**
     * ServiceManagerConfig constructor.
     * @param array $config
     */
    final public function __construct(array $config)
    {
        foreach ($this->types as $type) {
            if (!\array_key_exists($type, $config)) {
                $config[$type] = [];
                continue;
            }
        }

        $this->validate($config);

        $config = $this->handleConfigProviders($config);

        $this->config = $config;
    }

    /**
     * @param array $config
     */
    private function validate(array $config): void
    {
        foreach ($config as $key => $values) {
            if (!\in_array($key, $this->types)) {
                throw new InvalidArgumentException(\sprintf("'%s' is not a valid configuration key", $key));
            }

            $method = "validate" . \ucfirst($key);
            $this->{$method}($values);
        }
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
    private function validateConfigProviders(array $config): void
    {
        foreach ($config as $configProvider) {
            if (!\is_string($configProvider)) {
                throw new InvalidArgumentException(\sprintf("'%s' is not a valid config provider", \var_export($configProvider, true)));
            }
            $classImplements = @\class_implements($configProvider);
            if (!\is_array($classImplements)) {
                throw new InvalidArgumentException(\sprintf("ConfigProvider '%s' can't be loaded", $configProvider));
            }
            if (!\in_array(ConfigProviderInterface::class, $classImplements)) {
                throw new InvalidArgumentException(\sprintf("'%s' doesn't implement '%s'", $configProvider, ConfigProviderInterface::class));
            }
        }
    }

    private function handleConfigProviders(array $config): array
    {
        foreach ($config['configProviders'] as $configProvider) {
            $configProvider = new $configProvider();
            $serviceManagerConfig = $configProvider->getServiceManagerConfig();
            $config['factories'] = \array_merge($serviceManagerConfig->getFactories(), $config['factories']);
            $config['disabledSharing'] = \array_merge($serviceManagerConfig->getDisabledSharing(), $config['disabledSharing']);
            $config['delegators'] = \array_merge($serviceManagerConfig->getDelegators(), $config['delegators']);
            $config['initializers'] = \array_merge($serviceManagerConfig->getInitializers(), $config['initializers']);
            $config['lazyServices'] = \array_merge($serviceManagerConfig->getLazyServices(), $config['lazyServices']);
            $config['subManagers'] = \array_merge($serviceManagerConfig->getSubManagers(), $config['subManagers']);
        }

        return $config;
    }

    /**
     * @param string $key
     * @return array
     */
    private function getValue(string $key): array
    {
        return $this->config[$key];
    }

    /**
     * @return array
     */
    final public function getFactories(): array
    {
        return $this->getValue("factories");
    }

    /**
     * @return array
     */
    final public function getDisabledSharing(): array
    {
        return $this->getValue("disabledSharing");
    }

    /**
     * @return array
     */
    final public function getDelegators(): array
    {
        return $this->getValue("delegators");
    }

    /**
     * @return array
     */
    final public function getInitializers(): array
    {
        return $this->getValue("initializers");
    }

    /**
     * @return array
     */
    final public function getLazyServices(): array
    {
        return $this->getValue("lazyServices");
    }

    /**
     * @return array
     */
    final public function getSubManagers(): array
    {
        return $this->getValue("subManagers");
    }

    /**
     * @return array
     */
    final public function getConfigProviders(): array
    {
        return $this->getValue("configProviders");
    }

    /**
     * @return string
     */
    final public function serialize()
    {
        return \serialize($this->config);
    }


    final public function unserialize($serialized)
    {
        $this->config = \unserialize($serialized);
    }

    /**
     * @return array
     */
    final public function getConfig(): array
    {
        $factories = $this->getFactories();
        $factories = \array_merge($factories, $this->getSubManagers());

        return [
            'factories' => $factories,
            'delegators' => $this->getDelegators(),
            'shared' => \array_fill_keys($this->getDisabledSharing(), false),
            'initializers' => $this->getInitializers(),
            'shared_by_default' => true,
        ];
    }
}