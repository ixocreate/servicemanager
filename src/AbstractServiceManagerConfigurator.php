<?php
/**
 * @link https://github.com/ixocreate
 * @copyright IXOCREATE GmbH
 * @license MIT License
 */

declare(strict_types=1);

namespace Ixocreate\ServiceManager;

use Ixocreate\Application\ConfiguratorInterface;
use Ixocreate\ServiceManager\DelegatorFactoryInterface;
use Ixocreate\ServiceManager\FactoryInterface;
use Ixocreate\ServiceManager\InitializerInterface;
use Ixocreate\ServiceManager\Exception\InvalidArgumentException;
use Ixocreate\ServiceManager\Factory\AutowireFactory;
use Zend\Code\Reflection\FileReflection;
use Zend\ServiceManager\Proxy\LazyServiceFactory;

abstract class AbstractServiceManagerConfigurator implements ConfiguratorInterface, ServiceManagerConfiguratorInterface
{
    /**
     * @var array
     */
    private $factories = [];

    /**
     * @var array
     */
    private $delegators = [];

    /**
     * @var array
     */
    private $lazyServices = [];

    /**
     * @var array
     */
    private $initializers = [];

    /**
     * @var string;
     */
    private $defaultAutowireFactory;

    /**
     * @var array
     */
    private $directories = [];

    /**
     * ServiceManagerConfigurator constructor.
     * @param string $defaultAutowireFactory
     */
    public function __construct(string $defaultAutowireFactory = AutowireFactory::class)
    {
        //TODO check defaultAutowireFactory
        $this->defaultAutowireFactory = $defaultAutowireFactory;
    }

    /**
     * @param string $directory
     * @param bool $recursive
     * @param array $only
     */
    public function addDirectory(string $directory, bool $recursive = true, array $only = []) : void
    {
        $this->directories[] = [
            'dir' => $directory,
            'recursive' => $recursive,
            'only' => $only,
        ];
    }

    /**
     * @return array
     */
    public function getDirectories(): array
    {
        return $this->directories;
    }

    /**
     * @param string $name
     * @param null|string $factory
     */
    final public function addService(string $name, ?string $factory = null): void
    {
        $this->addFactory($name, $factory);
    }

    /**
     * @param string $name
     * @param string $factory
     */
    final public function addFactory(string $name, ?string $factory = null): void
    {
        if (empty($factory)) {
            $factory = $this->defaultAutowireFactory;
        }

        if (!\class_exists($factory)) {
            throw new InvalidArgumentException(\sprintf("Factory '%s' can't be loaded", $factory));
        }

        if (!\is_subclass_of($factory, FactoryInterface::class)) {
            throw new InvalidArgumentException(\sprintf("'%s' doesn't implement '%s'", $factory, FactoryInterface::class));
        }

        $this->factories[$name] = $factory;
    }

    /**
     * @return array
     */
    final public function getFactories(): array
    {
        return $this->factories;
    }

    /**
     * @param string $name
     * @param array $delegators
     */
    final public function addDelegator(string $name, array $delegators): void
    {
        foreach ($delegators as $delegator) {
            if (!\is_string($delegator)) {
                throw new InvalidArgumentException(\sprintf("'%s' is not a valid delegator", \var_export($delegator, true)));
            }

            if ($delegator === LazyServiceFactory::class) {
                continue;
            }

            if (!\is_subclass_of($delegator, DelegatorFactoryInterface::class)) {
                throw new InvalidArgumentException(\sprintf("'%s' doesn't implement '%s'", $delegator, DelegatorFactoryInterface::class));
            }
        }

        if (!\array_key_exists($name, $this->delegators)) {
            $this->delegators[$name] = $delegators;
            return;
        }

        $this->delegators[$name] += $delegators;
    }

    /**
     * @return array
     */
    final public function getDelegators(): array
    {
        return $this->delegators;
    }

    /**
     * @param string $name
     * @param string|null $className
     */
    final public function addLazyService(string $name, string $className = null): void
    {
        if ($className === null) {
            $className = $name;
        }

        if (!\class_exists($className)) {
            throw new InvalidArgumentException(\sprintf("'%s' is not a valid class", $className));
        }

        $this->lazyServices[$name] = $className;
        $this->addDelegator($name, [LazyServiceFactory::class]);
    }

    /**
     * @return array
     */
    final public function getLazyServices(): array
    {
        return $this->lazyServices;
    }

    /**
     * @param string $name
     */
    final public function addInitializer(string $name): void
    {
        if (!\is_subclass_of($name, InitializerInterface::class)) {
            throw new InvalidArgumentException(\sprintf("'%s' doesn't implement '%s'", $name, InitializerInterface::class));
        }

        $this->initializers[] = $name;
    }

    /**
     * @return array
     */
    final public function getInitializers(): array
    {
        return $this->initializers;
    }

    /**
     * @return array
     */
    public function getSubManagers(): array
    {
        return [];
    }

    public function getMetadata(): array
    {
        return [];
    }

    /**
     *
     */
    protected function processDirectories(): void
    {
        foreach ($this->directories as $item) {
            if (!\is_dir($item['dir'])) {
                continue;
            }

            $this->scanDirectory($item['dir'], $item['recursive'], $item['only']);
        }
    }

    /**
     * @param string $directory
     * @param bool $recursive
     * @param array $only
     */
    protected function scanDirectory(string $directory, bool $recursive, array $only): void
    {
        $entries = \scandir($directory);
        foreach ($entries as $entry) {
            if ($entry === "." || $entry === "..") {
                continue;
            }

            if (\is_dir($directory . '/' . $entry)) {
                if ($recursive === true) {
                    $this->scanDirectory($directory . '/' . $entry, $recursive, $only);
                }
                continue;
            }

            $fileinfo = \pathinfo($directory . '/' . $entry);

            if (empty($fileinfo['extension']) || $fileinfo['extension'] !== 'php') {
                continue;
            }

            try {
                $fileReflection = new FileReflection($directory . '/' . $entry, true);
                foreach ($fileReflection->getClasses() as $class) {
                    if (\array_key_exists($class->getName(), $this->factories)) {
                        continue;
                    }

                    if ($class->isAbstract()) {
                        continue;
                    }
                    if ($class->isInterface()) {
                        continue;
                    }

                    if (!empty($only)) {
                        $check = false;

                        foreach ($only as $instanceCheck) {
                            if (\interface_exists($instanceCheck) && $class->implementsInterface($instanceCheck)) {
                                $check = true;
                                break;
                            } elseif ($class->isSubclassOf($instanceCheck)) {
                                $check = true;
                                break;
                            }
                        }

                        if ($check === false) {
                            continue;
                        }
                    }

                    $this->addFactory($class->getName());
                }
            } catch (\Exception $e) {
            }
        }
    }

    /**
     * @return ServiceManagerConfig
     */
    public function getServiceManagerConfig(): ServiceManagerConfig
    {
        $this->processDirectories();
        return new ServiceManagerConfig($this);
    }
}
