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
namespace KiwiSuite\ServiceManager;

use KiwiSuite\ServiceManager\Factory\AutowireFactory;
use Zend\Code\Reflection\FileReflection;
use Zend\ServiceManager\Proxy\LazyServiceFactory;

final class ServiceManagerConfigurator
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
    private $disabledSharing = [];

    /**
     * @var array
     */
    private $lazyServices = [];

    /**
     * @var array
     */
    private $initializers = [];

    /**
     * @var array
     */
    private $subManagers = [];

    /**
     * @var string;
     */
    private $defaultAutowireFactory;

    /**
     * @var array
     */
    private $directories = [];

    /**
     * @var string
     */
    private $serviceManagerConfigClass;

    /**
     * ServiceManagerConfigurator constructor.
     * @param string $serviceManagerConfigClass
     * @param string $defaultAutowireFactory
     */
    public function __construct(
        string $serviceManagerConfigClass = ServiceManagerConfig::class,
        string $defaultAutowireFactory = AutowireFactory::class
    ) {
        //TODO check servicemanagerConfig class and defaultAutowireFactory
        $this->defaultAutowireFactory = $defaultAutowireFactory;
        $this->serviceManagerConfigClass = $serviceManagerConfigClass;
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
     * @param string $name
     * @param string $factory
     */
    public function addFactory(string $name, ?string $factory = null): void
    {
        if (empty($factory)) {
            $factory = $this->defaultAutowireFactory;
        }

        $this->factories[$name] = $factory;
    }

    /**
     * @return array
     */
    public function getFactories(): array
    {
        return $this->factories;
    }

    /**
     * @param string $name
     * @param array $delegators
     */
    public function addDelegator(string $name, array $delegators): void
    {
        if (!\array_key_exists($name, $this->delegators)) {
            $this->delegators[$name] = $delegators;
            return;
        }

        $this->delegators[$name] += $delegators;
    }

    /**
     * @return array
     */
    public function getDelegators(): array
    {
        return $this->delegators;
    }

    /**
     * @param string $name
     * @param string|null $className
     */
    public function addLazyService(string $name, string $className = null): void
    {
        if ($className === null) {
            $className = $name;
        }

        $this->lazyServices[$name] = $className;
        $this->addDelegator($name, [LazyServiceFactory::class]);
    }

    /**
     * @return array
     */
    public function getLazyServices(): array
    {
        return $this->lazyServices;
    }

    /**
     * @param string $name
     */
    public function addInitializer(string $name): void
    {
        $this->initializers[] = $name;
    }

    /**
     * @return array
     */
    public function getInitializers(): array
    {
        return $this->initializers;
    }

    /**
     * @param string $name
     */
    public function disableSharingFor(string $name): void
    {
        $this->disabledSharing[] = $name;
    }

    /**
     * @return array
     */
    public function getDisableSharing(): array
    {
        return $this->disabledSharing;
    }

    /**
     * @param string $manager
     * @param string $factory
     */
    public function addSubManager(string $manager, string $factory): void
    {
        $this->subManagers[$manager] = $factory;
    }

    /**
     * @return array
     */
    public function getSubManagers(): array
    {
        return $this->subManagers;
    }

    /**
     * @return ServiceManagerConfig
     */
    public function getServiceManagerConfig(): ServiceManagerConfig
    {
        $this->processDirectories();

        return new $this->serviceManagerConfigClass([
            'factories' => $this->getFactories(),
            'initializers' => $this->getInitializers(),
            'delegators' => $this->getDelegators(),
            'subManagers' => $this->getSubManagers(),
            'lazyServices' => $this->getLazyServices(),
            'disabledSharing' => $this->getDisableSharing(),
        ]);
    }

    /**
     *
     */
    private function processDirectories(): void
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
    private function scanDirectory(string $directory, bool $recursive, array $only): void
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
                    if (!\array_key_exists($class->getName(), $this->factories)) {
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
}
