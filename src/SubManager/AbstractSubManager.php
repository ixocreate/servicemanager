<?php
/**
 * @link https://github.com/ixocreate
 * @copyright IXOLIT GmbH
 * @license MIT License
 */

declare(strict_types=1);

namespace Ixocreate\ServiceManager\SubManager;

use Interop\Container\ContainerInterface;
use Ixocreate\ServiceManager\Autowire\FactoryResolver\FactoryResolverInterface;
use Ixocreate\ServiceManager\Exception\ServiceNotCreatedException;
use Ixocreate\ServiceManager\Exception\ServiceNotFoundException;
use Ixocreate\ServiceManager\OriginalServiceManager;
use Ixocreate\ServiceManager\ServiceManagerConfigInterface;
use Ixocreate\ServiceManager\ServiceManagerInterface;
use Ixocreate\ServiceManager\ServiceManagerSetupInterface;

abstract class AbstractSubManager implements ServiceManagerInterface, ContainerInterface, SubManagerInterface
{
    /**
     * @var string
     */
    private $instanceOf;

    /**
     * @var ServiceManagerInterface
     */
    protected $creationContext;

    /**
     * @var OriginalServiceManager
     */
    private $serviceManager;

    /**
     * @var ServiceManagerConfigInterface
     */
    private $serviceManagerConfig;

    /**
     * @var array
     */
    private $initialServices = [];

    /**
     * @param ServiceManagerInterface $serviceManager
     * @param ServiceManagerConfigInterface $serviceManagerConfig
     * @param array $services
     * @param string $validation
     */
    public function __construct(
        ServiceManagerInterface $serviceManager,
        ServiceManagerConfigInterface $serviceManagerConfig,
        array $services = [],
        string $validation = null
    ) {
        $this->creationContext = $serviceManager;
        $this->serviceManagerConfig = $serviceManagerConfig;
        $this->initialServices = $services;

        if ($validation !== null) {
            $this->instanceOf = $validation;
        } else {
            $this->instanceOf = static::getValidation();
        }

        $this->serviceManager = new OriginalServiceManager($serviceManager, $serviceManagerConfig, $serviceManager->serviceManagerSetup(), $services);
    }

    /**
     * @param string $id
     * @throws ServiceNotFoundException
     * @throws ServiceNotCreatedException
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

        $this->validate($instance);

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
     * @throws ServiceNotFoundException
     * @throws ServiceNotCreatedException
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

        $this->validate($instance);

        return $instance;
    }

    private function resolveService(string $id): string
    {
        if (\array_key_exists($id, $this->serviceManagerConfig->getNamedServices())) {
            return $this->serviceManagerConfig->getNamedServices()[$id];
        }

        return $id;
    }

    /**
     * @param object $instance
     */
    private function validate($instance): void
    {
        if (empty($this->instanceOf) || $instance instanceof $this->instanceOf) {
            return;
        }

        throw new ServiceNotCreatedException(\sprintf(
            'Plugin manager "%s" expected an instance of type "%s", but "%s" was received',
            __CLASS__,
            $this->instanceOf,
            \is_object($instance) ? \get_class($instance) : \gettype($instance)
        ));
    }

    /**
     * @return string
     */
    public static function getValidation(): ?string
    {
        return null;
    }

    /**
     * @return FactoryResolverInterface
     */
    final public function factoryResolver(): FactoryResolverInterface
    {
        return $this->creationContext->factoryResolver();
    }

    /**
     * @return ServiceManagerConfigInterface
     */
    final public function serviceManagerConfig(): ServiceManagerConfigInterface
    {
        return $this->serviceManagerConfig;
    }

    /**
     * @return ServiceManagerSetupInterface
     */
    final public function serviceManagerSetup(): ServiceManagerSetupInterface
    {
        return $this->creationContext->serviceManagerSetup();
    }

    /**
     * @return array
     */
    final public function services(): array
    {
        return \array_keys($this->serviceManagerConfig->getFactories());
    }

    /**
     * @return array
     */
    final public function initialServices(): array
    {
        return $this->initialServices;
    }
}
