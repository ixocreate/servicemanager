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
namespace KiwiSuite\ServiceManager\SubManager;

use KiwiSuite\ServiceManager\Autowire\FactoryResolver\FactoryResolverInterface;
use KiwiSuite\ServiceManager\Exception\ServiceNotCreatedException;
use KiwiSuite\ServiceManager\Exception\ServiceNotFoundException;
use KiwiSuite\ServiceManager\ServiceManager;
use KiwiSuite\ServiceManager\ServiceManagerConfig;
use KiwiSuite\ServiceManager\ServiceManagerSetup;

class SubManager implements SubManagerInterface
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
     * @var ServiceManagerSetup
     */
    private $serviceManagerSetup;

    /**
     * @var ServiceManagerConfig
     */
    private $serviceManagerConfig;

    /**
     * @var FactoryResolverInterface
     */
    private $factoryResolver;

    /**
     * @param ServiceManager $serviceManager
     * @param ServiceManagerConfig $serviceManagerConfig
     * @param string $validation
     */
    final public function __construct(ServiceManager $serviceManager, ServiceManagerConfig $serviceManagerConfig, string $validation)
    {
        $this->serviceManager = new PluginManager(
            $serviceManager,
            $serviceManagerConfig->getConfig()
        );

        $this->validation = $validation;
        $this->serviceManagerSetup = $serviceManager->getServiceManagerSetup();
        $this->serviceManagerConfig = $serviceManagerConfig;
        $this->factoryResolver = $serviceManager->getFactoryResolver();
    }

    /**
     * @param string $id
     * @throws ServiceNotCreatedException
     * @throws ServiceNotFoundException
     * @return mixed
     */
    final public function get($id)
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
    final public function has($id): bool
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
    final public function build(string $id, array $options = null)
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

    /**
     * @return string
     */
    final public function getValidation(): string
    {
        return $this->validation;
    }

    /**
     * @return ServiceManagerSetup
     */
    final public function getServiceManagerSetup(): ServiceManagerSetup
    {
        return $this->serviceManagerSetup;
    }

    /**
     * @return ServiceManagerConfig
     */
    final public function getServiceManagerConfig(): ServiceManagerConfig
    {
        return $this->serviceManagerConfig;
    }

    /**
     * @return FactoryResolverInterface
     */
    final public function getFactoryResolver(): FactoryResolverInterface
    {
        return $this->factoryResolver;
    }
}
