<?php
/**
 * @link https://github.com/ixocreate
 * @copyright IXOLIT GmbH
 * @license MIT License
 */

declare(strict_types=1);

namespace Ixocreate\ServiceManager;

use Interop\Container\ContainerInterface;
use Ixocreate\ServiceManager\Autowire\Autoloader;
use Ixocreate\ServiceManager\Autowire\DependencyResolver;
use Ixocreate\ServiceManager\Autowire\FactoryCode;
use Ixocreate\ServiceManager\Autowire\FactoryResolver\FileFactoryResolver;
use Ixocreate\ServiceManager\Autowire\FactoryResolver\RuntimeFactoryResolver;
use Ixocreate\ServiceManager\Autowire\FactoryResolverInterface;
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
     * @var ServiceManagerSetup
     */
    private $serviceManagerSetup;

    /**
     * @var FactoryResolverInterface;
     */
    private $factoryResolver;

    /**
     * @var array
     */
    private $initalServices = [];

    /**
     * @param ServiceManagerConfigInterface $serviceManagerConfig
     * @param ServiceManagerSetupInterface $serviceManagerSetup
     * @param array $services
     */
    public function __construct(
        ServiceManagerConfigInterface $serviceManagerConfig,
        ServiceManagerSetupInterface $serviceManagerSetup,
        array $services = []
    ) {
        $this->serviceManagerConfig = $serviceManagerConfig;

        $this->initalServices = $services;

        $config = $serviceManagerConfig->getConfig();
        $config['services'] = $services;
        $config['lazy_services'] = [
            'class_map' => $serviceManagerConfig->getLazyServices(),
            'proxies_target_dir' => null,
            'proxies_namespace' => null,
            'write_proxy_files' => false,
        ];
        $config['aliases'] = $serviceManagerConfig->getNamedServices();

        if ($serviceManagerSetup->isPersistLazyLoading()) {
            if (!\file_exists($serviceManagerSetup->getLazyLoadingLocation())) {
                @\mkdir($serviceManagerSetup->getLazyLoadingLocation(), 0777, true);
            }

            $config['lazy_services']['proxies_target_dir'] = $serviceManagerSetup->getLazyLoadingLocation();
            $config['lazy_services']['write_proxy_files'] = true;
        }

        $this->serviceManager = new OriginalServiceManager($this, $config);
        $this->serviceManagerSetup = $serviceManagerSetup;
    }

    /**
     * @return array
     */
    public function initialServices(): array
    {
        return $this->initalServices;
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
     * @return ServiceManagerConfigInterface
     */
    public function getServiceManagerConfig(): ServiceManagerConfigInterface
    {
        return $this->serviceManagerConfig;
    }

    /**
     * @return ServiceManagerSetupInterface
     */
    public function getServiceManagerSetup(): ServiceManagerSetupInterface
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

    /**
     * @return array
     */
    public function getServices(): array
    {
        return \array_keys($this->getServiceManagerConfig()->getFactories());
    }
}
