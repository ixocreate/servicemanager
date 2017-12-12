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
namespace KiwiSuite\ServiceManager;

use KiwiSuite\ServiceManager\Autowire\Autoloader;
use KiwiSuite\ServiceManager\Autowire\DependencyResolver;
use KiwiSuite\ServiceManager\Autowire\FactoryCode;
use KiwiSuite\ServiceManager\Autowire\FactoryResolver\FactoryResolverInterface;
use KiwiSuite\ServiceManager\Autowire\FactoryResolver\FileFactoryResolver;
use KiwiSuite\ServiceManager\Autowire\FactoryResolver\RuntimeFactoryResolver;
use KiwiSuite\ServiceManager\Exception\ServiceNotCreatedException;
use KiwiSuite\ServiceManager\Exception\ServiceNotFoundException;
use ProxyManager\Factory\LazyLoadingValueHolderFactory;
use KiwiSuite\ServiceManager\Factory\LazyLoadingValueHolderFactory as KiwiLazyLoadingValueHolderFactory;
use Zend\Di\Definition\RuntimeDefinition;

final class ServiceManager implements ServiceManagerInterface
{
    /**
     * @var OriginalServiceManager
     */
    private $serviceManager;

    /**
     * @var ServiceManagerConfig
     */
    private $serviceManagerConfig;

    /**
     * @var ServiceManagerSetup
     */
    private $serviceManagerSetup;

    /**
     * @var FactoryResolverInterface;
     */
    private $factoryResolver;

    /**
     * @param ServiceManagerConfig $serviceManagerConfig
     * @param ServiceManagerSetup $serviceManagerSetup
     * @param array $services
     */
    public function __construct(
        ServiceManagerConfig $serviceManagerConfig,
        ServiceManagerSetup $serviceManagerSetup,
        array $services = []
    ) {
        $this->serviceManagerConfig = $serviceManagerConfig;

        $config = $serviceManagerConfig->getConfig();
        $config['services'] = $services;
        $config['factories'][LazyLoadingValueHolderFactory::class] = KiwiLazyLoadingValueHolderFactory::class;

        $this->serviceManager = new OriginalServiceManager($this, $config);
        $this->serviceManagerSetup = $serviceManagerSetup;
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
            return $this->serviceManager->get($id);
        } catch (\Zend\ServiceManager\Exception\ServiceNotFoundException $exception) {
            throw new ServiceNotFoundException($exception->getMessage(), $exception->getCode(), $exception);
        } catch (\Zend\ServiceManager\Exception\ServiceNotCreatedException $exception) {
            throw new ServiceNotCreatedException($exception->getMessage(), $exception->getCode(), $exception);
        }
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
            return $this->serviceManager->build($id, $options);
        } catch (\Zend\ServiceManager\Exception\ServiceNotFoundException $exception) {
            throw new ServiceNotFoundException($exception->getMessage(), $exception->getCode(), $exception);
        } catch (\Zend\ServiceManager\Exception\ServiceNotCreatedException $exception) {
            throw new ServiceNotCreatedException($exception->getMessage(), $exception->getCode(), $exception);
        }
    }

    /**
     * @return ServiceManagerConfig
     */
    public function getServiceManagerConfig(): ServiceManagerConfig
    {
        return $this->serviceManagerConfig;
    }

    /**
     * @return ServiceManagerSetup
     */
    public function getServiceManagerSetup(): ServiceManagerSetup
    {
        return $this->serviceManagerSetup;
    }

    /**
     * @return FactoryResolverInterface
     */
    public function getFactoryResolver(): FactoryResolverInterface
    {
        if ($this->factoryResolver === null) {
            $factoryCode = new FactoryCode();
            if ($this->getServiceManagerSetup()->isPersistAutowire()) {
                $autoloader = new Autoloader($this);
                \spl_autoload_register($autoloader);

                $this->factoryResolver = new FileFactoryResolver($factoryCode);
            } else {
                $resolver = new DependencyResolver(new RuntimeDefinition());
                $resolver->setContainer($this);

                $this->factoryResolver = new RuntimeFactoryResolver($resolver, $factoryCode);
            }
        }
        return $this->factoryResolver;
    }
}
