<?php
/**
 * @link https://github.com/ixocreate
 * @copyright IXOLIT GmbH
 * @license MIT License
 */

declare(strict_types=1);

namespace Ixocreate\ServiceManager;

use Interop\Container\ContainerInterface;
use Ixocreate\ServiceManager\Autowire\DependencyResolver;
use Ixocreate\ServiceManager\Autowire\FactoryCode;
use Ixocreate\ServiceManager\Autowire\FactoryResolver\FactoryResolverInterface;
use Ixocreate\ServiceManager\Autowire\FactoryResolver\FileFactoryResolver;
use Ixocreate\ServiceManager\Autowire\FactoryResolver\RuntimeFactoryResolver;
use Ixocreate\ServiceManager\Exception\ServiceNotCreatedException;
use Ixocreate\ServiceManager\Exception\ServiceNotFoundException;
use Zend\Di\Definition\RuntimeDefinition;

final class ServiceManager implements ServiceManagerInterface, ContainerInterface
{
    /**
     * @var OriginalServiceManager
     */
    private $serviceManager;

    /**
     * @var ServiceManagerConfigInterface
     */
    private $serviceManagerConfig;

    /**
     * @var ServiceManagerSetupInterface
     */
    private $serviceManagerSetup;

    /**
     * @var FactoryResolverInterface;
     */
    private $factoryResolver;

    /**
     * @var array
     */
    private $initialServices = [];

    /**
     * @param ServiceManagerConfigInterface $serviceManagerConfig
     * @param ServiceManagerSetupInterface $serviceManagerSetup
     * @param array $services
     * @param mixed $creationContext
     */
    public function __construct(
        ServiceManagerConfigInterface $serviceManagerConfig,
        ServiceManagerSetupInterface $serviceManagerSetup,
        array $services = [],
        $creationContext = null
    ) {
        $this->serviceManagerConfig = $serviceManagerConfig;
        $this->serviceManagerSetup = $serviceManagerSetup;

        $this->initialServices = $services;

        $this->serviceManager = new OriginalServiceManager($creationContext ?? $this, $serviceManagerConfig, $serviceManagerSetup, $services);
    }

    /**
     * @param string $id
     * @throws ServiceNotFoundException
     * @throws ServiceNotCreatedException
     * @return mixed
     */
    public function get($id)
    {
        $id = $this->resolveService($id);

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
        $id = $this->resolveService($id);

        return $this->serviceManager->has($id);
    }

    /**
     * @param string $id
     * @param array|null $options
     * @throws ServiceNotFoundException
     * @throws ServiceNotCreatedException
     * @return mixed
     */
    public function build(string $id, array $options = null)
    {
        $id = $this->resolveService($id);

        try {
            return $this->serviceManager->build($id, $options);
        } catch (\Zend\ServiceManager\Exception\ServiceNotFoundException $exception) {
            throw new ServiceNotFoundException($exception->getMessage(), $exception->getCode(), $exception);
        } catch (\Zend\ServiceManager\Exception\ServiceNotCreatedException $exception) {
            throw new ServiceNotCreatedException($exception->getMessage(), $exception->getCode(), $exception);
        }
    }

    private function resolveService(string $id): string
    {
        if (\array_key_exists($id, $this->serviceManagerConfig->getNamedServices())) {
            return $this->serviceManagerConfig->getNamedServices()[$id];
        }

        return $id;
    }

    /**
     * @return ServiceManagerConfigInterface
     */
    public function serviceManagerConfig(): ServiceManagerConfigInterface
    {
        return $this->serviceManagerConfig;
    }

    /**
     * @return ServiceManagerSetupInterface
     */
    public function serviceManagerSetup(): ServiceManagerSetupInterface
    {
        return $this->serviceManagerSetup;
    }

    /**
     * @return FactoryResolverInterface
     */
    public function factoryResolver(): FactoryResolverInterface
    {
        if ($this->factoryResolver === null) {
            $factoryCode = new FactoryCode();
            if ($this->serviceManagerSetup->isPersistAutowire()) {
                $this->factoryResolver = new FileFactoryResolver($factoryCode);
            } else {
                $resolver = new DependencyResolver(new RuntimeDefinition());
                $resolver->setContainer($this);

                $this->factoryResolver = new RuntimeFactoryResolver($resolver, $factoryCode);
            }
        }
        return $this->factoryResolver;
    }

    /**
     * @return array
     */
    public function services(): array
    {
        return \array_keys($this->serviceManagerConfig->getFactories());
    }

    /**
     * @return array
     */
    public function initialServices(): array
    {
        return $this->initialServices;
    }
}
