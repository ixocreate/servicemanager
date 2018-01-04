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
namespace KiwiSuite\ServiceManager\Autowire;

use Psr\Container\ContainerInterface;
use Zend\Di\Definition\DefinitionInterface;
use Zend\Di\Resolver\DependencyResolverInterface;
use Zend\Di\Resolver\ValueInjection;

final class DependencyResolver implements DependencyResolverInterface
{
    /**
     * @var DefinitionInterface
     */
    protected $definition;

    /**
     * @var ContainerInterface
     */
    protected $container = null;

    /**
     * DependencyResolver constructor.
     * @param DefinitionInterface $definition
     */
    public function __construct(DefinitionInterface $definition)
    {
        $this->definition = $definition;
    }

    /**
     * @param ContainerInterface $container
     * @return $this|DependencyResolverInterface
     */
    public function setContainer(ContainerInterface $container)
    {
        $this->container = $container;
        return $this;
    }

    /**
     * @param string $requestedType
     * @param array $callTimeParameters
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     * @return array
     */
    public function resolveParameters(string $requestedType, array $callTimeParameters = []) : array
    {
        $definition = $this->definition->getClassDefinition($requestedType);
        $params = $definition->getParameters();
        $result = [];

        if (empty($params)) {
            return $result;
        }

        foreach ($params as $paramInfo) {
            $name = $paramInfo->getName();
            $type = $paramInfo->getType();

            if (isset($callTimeParameters[$name])) {
                $result[$name] = new ValueInjection($callTimeParameters[$name]);
                continue;
            }

            if ($type && !$paramInfo->isBuiltin()) {
                if ($this->container->has($type)) {
                    $result[$name] = new ContainerInjection($type, null);
                    continue;
                }

                foreach (\array_keys($this->container->getServiceManagerConfig()->getSubManagers()) as $serviceName) {
                    if ($this->container->get($serviceName)->has($type)) {
                        $result[$name] = new ContainerInjection($type, $serviceName);
                        continue 2;
                    }
                }

                foreach (\array_keys($this->container->getServiceManagerConfig()->getSubManagers()) as $serviceName) {
                    if ($this->container->get($serviceName)->has($name) && $type === $this->container->get($serviceName)->getValidation()) {
                        $result[$name] = new ContainerInjection($name, $serviceName);
                        continue 2;
                    }
                }

                if ($this->container->has($name)) {
                    $result[$name] = new ContainerInjection($name, null);
                    continue;
                }
            }

            if (!$paramInfo->isRequired()) {
                $result[$name] = new DefaultValueInjection($paramInfo->getDefault());
                continue;
            }

            $result[$name] = new ValueInjection(null);
        }

        foreach ($result as $name => $injection) {
            $injection->setParameterName($name);
        }

        return $result;
    }

    /**
     * @param string $type
     * @param null|string $context
     * @return null|string
     */
    public function resolvePreference(string $type, ?string $context = null) : ?string
    {
        return null;
    }
}
