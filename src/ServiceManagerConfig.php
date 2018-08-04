<?php
/**
 * kiwi-suite/servicemanager (https://github.com/kiwi-suite/servicemanager)
 *
 * @package kiwi-suite/servicemanager
 * @link https://github.com/kiwi-suite/servicemanager
 * @copyright Copyright (c) 2010 - 2018 kiwi suite GmbH
 * @license MIT License
 */

declare(strict_types=1);

namespace KiwiSuite\ServiceManager;

use KiwiSuite\Contract\ServiceManager\NamedServiceInterface;
use KiwiSuite\Contract\ServiceManager\ServiceManagerConfigInterface;

final class ServiceManagerConfig implements ServiceManagerConfigInterface
{
    /**
     * @var array
     */
    private $config;

    /**
     * ServiceManagerConfig constructor.
     * @param ServiceManagerConfiguratorInterface $serviceManagerConfigurator
     */
    public function __construct(ServiceManagerConfiguratorInterface $serviceManagerConfigurator)
    {
        $this->config['factories'] = $serviceManagerConfigurator->getFactories();
        $this->config['delegators'] = $serviceManagerConfigurator->getDelegators();
        $this->config['lazyServices'] = $serviceManagerConfigurator->getLazyServices();
        $this->config['initializers'] = $serviceManagerConfigurator->getInitializers();
        $this->config['subManagers'] = $serviceManagerConfigurator->getSubManagers();
        $this->config['metadata'] = $serviceManagerConfigurator->getMetadata();

        $this->config['namedServices'] = [];

        foreach (\array_keys($this->config['factories']) as $service) {
            if (!\is_subclass_of($service, NamedServiceInterface::class, true)) {
                continue;
            }
            $this->config['namedServices'][\forward_static_call([$service, 'serviceName'])] = $service;
        }
    }

    /**
     * @return array
     */
    public function getFactories(): array
    {
        return $this->config['factories'];
    }

    /**
     * @return array
     */
    public function getDisabledSharing(): array
    {
        return $this->config['disabledSharing'];
    }

    /**
     * @return array
     */
    public function getDelegators(): array
    {
        return $this->config['delegators'];
    }

    /**
     * @return array
     */
    public function getInitializers(): array
    {
        return $this->config['initializers'];
    }

    /**
     * @return array
     */
    public function getLazyServices(): array
    {
        return $this->config['lazyServices'];
    }

    /**
     * @return array
     */
    public function getSubManagers(): array
    {
        return $this->config['subManagers'];
    }

    /**
     * @return array
     */
    public function getNamedServices(): array
    {
        return $this->config['namedServices'];
    }

    /**
     * @param string $name
     * @param mixed $default
     * @return array
     */
    public function getMetadata(string $name = null, $default = null)
    {
        if ($name !== null) {
            if (!\array_key_exists($name, $this->config['metadata'])) {
                return $default;
            }

            return $this->config['metadata'][$name];
        }

        return $this->config['metadata'];
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
    public function getConfig(): array
    {
        $factories = \array_merge($this->config['factories'], $this->config['subManagers']);

        return [
            'factories' => $factories,
            'delegators' => $this->getDelegators(),
            'initializers' => $this->getInitializers(),
            'shared_by_default' => true,
        ];
    }
}
