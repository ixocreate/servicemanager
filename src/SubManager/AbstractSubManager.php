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
     * @var string|null
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
            $this->instanceOf = static::validation();
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
        try {
            $instance = $this->serviceManager->get($id);
        } catch (\Zend\ServiceManager\Exception\ServiceNotFoundException $exception) {
            throw new ServiceNotFoundException($exception->getMessage(), $exception->getCode(), $exception->getPrevious());
        } catch (\Zend\ServiceManager\Exception\ServiceNotCreatedException $exception) {
            throw new ServiceNotCreatedException($exception->getMessage(), $exception->getCode(), $exception->getPrevious());
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
        try {
            $instance = $this->serviceManager->build($id, $options);
        } catch (\Zend\ServiceManager\Exception\ServiceNotFoundException $exception) {
            throw new ServiceNotFoundException($exception->getMessage(), $exception->getCode(), $exception);
        } catch (\Zend\ServiceManager\Exception\ServiceNotCreatedException $exception) {
            throw new ServiceNotCreatedException($exception->getMessage(), $exception->getCode(), $exception->getPrevious());
        }

        $this->validate($instance);

        return $instance;
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
     * @deprecated
     */
    public function getValidation(): ?string
    {
        return ($this->instanceOf) ?? static::validation();
    }

    /**
     * @return string
     */
    public static function validation(): ?string
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
     * @deprecated use serviceManagerConfig()
     */
    final public function getServiceManagerConfig(): ServiceManagerConfigInterface
    {
        return $this->serviceManagerConfig;
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
     * @deprecated Use services()
     */
    final public function getServices(): array
    {
        return \array_keys($this->serviceManagerConfig->getFactories());
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
