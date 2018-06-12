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

use KiwiSuite\Contract\ServiceManager\Autowire\FactoryResolverInterface;
use KiwiSuite\Contract\ServiceManager\ServiceManagerConfigInterface;
use KiwiSuite\Contract\ServiceManager\ServiceManagerSetupInterface;
use KiwiSuite\Contract\ServiceManager\SubManager\SubManagerInterface;
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
        $config = $serviceManagerConfig->getConfig();
        $config['lazy_services'] = [
            'class_map' => $serviceManagerConfig->getLazyServices(),
            'proxies_target_dir' => null,
            'proxies_namespace' => null,
            'write_proxy_files' => false,
        ];

        if ($serviceManager->getServiceManagerSetup()->isPersistLazyLoading()) {
            if (!\file_exists($serviceManager->getServiceManagerSetup()->getLazyLoadingLocation())) {
                @\mkdir($serviceManager->getServiceManagerSetup()->getLazyLoadingLocation(), 0777, true);
            }

            $config['lazy_services']['proxies_target_dir'] = $serviceManager->getServiceManagerSetup()->getLazyLoadingLocation();
            $config['lazy_services']['write_proxy_files'] = true;
        }

        $this->serviceManager = new PluginManager(
            $serviceManager,
            $config
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
        $id = $this->resolveService($id);

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
        $id = $this->resolveService($id);

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
        $id = $this->resolveService($id);

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

    private function resolveService(string $id): string
    {
        if (\array_key_exists($id, $this->getServiceManagerConfig()->getNamedServices())) {
            return $this->getServiceManagerConfig()->getNamedServices()[$id];
        }

        return $id;
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
     * @return ServiceManagerSetupInterface
     */
    final public function getServiceManagerSetup(): ServiceManagerSetupInterface
    {
        return $this->serviceManagerSetup;
    }

    /**
     * @return ServiceManagerConfigInterface
     */
    final public function getServiceManagerConfig(): ServiceManagerConfigInterface
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

    /**
     * @return array
     */
    final public function getServices(): array
    {
        return \array_keys($this->getServiceManagerConfig()->getFactories());
    }
}
