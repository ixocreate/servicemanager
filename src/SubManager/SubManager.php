<?php
/**
 * kiwi-suite/servicemanager (https://github.com/kiwi-suite/servicemanager)
 *
 * @package kiwi-suite/servicemanager
 * @see https://github.com/kiwi-suite/servicemanager
 * @copyright Copyright (c) 2010 - 2017 kiwi suite GmbH
 * @license MIT License
 */

declare(strict_types=1);
namespace KiwiSuite\ServiceManager\SubManager;

use KiwiSuite\ServiceManager\Exception\ServiceNotCreatedException;
use KiwiSuite\ServiceManager\Exception\ServiceNotFoundException;
use KiwiSuite\ServiceManager\ServiceManager;
use KiwiSuite\ServiceManager\ServiceManagerConfig;

final class SubManager implements SubManagerInterface
{

    /**
     * @var PluginManager
     */
    private $serviceManager;

    /**
     * @var string
     */
    private $validation;

    /**
     * @param ServiceManager $serviceManager
     * @param ServiceManagerConfig $serviceManagerConfig
     * @param string $validation
     */
    public function __construct(ServiceManager $serviceManager, ServiceManagerConfig $serviceManagerConfig, string $validation)
    {
        $this->serviceManager = new PluginManager(
            $serviceManager,
            [
            'services' => $serviceManagerConfig->getServices(),
            'factories' => $serviceManagerConfig->getFactories(),
            'delegators' => $serviceManagerConfig->getDelegators(),
            'shared' => \array_fill_keys($serviceManagerConfig->getDisabledSharing(), false),
            'lazy_services' => [
                'class_map' => $serviceManagerConfig->getLazyServices(),
                'proxies_target_dir' => null,
                'proxies_namespace' => null,
                'write_proxy_files' => false,
            ],
            'initializers' => $serviceManagerConfig->getInitializers(),
            'shared_by_default' => true,
        ]
        );

        $this->validation = $validation;
    }

    /**
     * @param string $id
     * @throws ServiceNotCreatedException
     * @throws ServiceNotFoundException
     * @return mixed
     */
    public function get($id)
    {
        try {
            $instance = $this->serviceManager->get($id);
        } catch (\Zend\ServiceManager\Exception\ServiceNotFoundException $exception) {
            throw new ServiceNotFoundException($exception->getMessage(), $exception->getCode(), $exception);
        } catch (\Zend\ServiceManager\Exception\ServiceNotCreatedException $exception) {
            throw new ServiceNotCreatedException($exception->getMessage(), $exception->getCode(), $exception);
        }

        if (!$this->validate($instance)) {
            throw new ServiceNotCreatedException(
                \sprintf("'%s' isn't an instance of '%s'", $id, $this->validation)
            );
        }

        return $instance;
    }

    /**
     * @param string $id
     * @return bool
     */
    public function has($id): bool
    {
        return $this->serviceManager->has($id);
    }

    /**
     * @param string $id
     * @param array|null $options
     * @throws ServiceNotCreatedException
     * @throws ServiceNotFoundException
     * @return mixed
     */
    public function build(string $id, array $options = null)
    {
        try {
            $instance = $this->serviceManager->build($id, $options);
        } catch (\Zend\ServiceManager\Exception\ServiceNotFoundException $exception) {
            throw new ServiceNotFoundException($exception->getMessage(), $exception->getCode(), $exception);
        } catch (\Zend\ServiceManager\Exception\ServiceNotCreatedException $exception) {
            throw new ServiceNotCreatedException($exception->getMessage(), $exception->getCode(), $exception);
        }

        if (!$this->validate($instance)) {
            throw new ServiceNotCreatedException(
                \sprintf("'%s' isn't an instance of '%s'", $id, $this->validation)
            );
        }

        return $instance;
    }

    /**
     * @param object $instance
     * @return bool
     */
    private function validate($instance): bool
    {
        return $instance instanceof $this->validation;
    }
}
