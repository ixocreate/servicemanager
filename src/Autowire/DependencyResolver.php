<?php
/**
 * @link https://github.com/ixocreate
 * @copyright IXOLIT GmbH
 * @license MIT License
 */

declare(strict_types=1);

namespace Ixocreate\ServiceManager\Autowire;

use Ixocreate\ServiceManager\Exception\InvalidArgumentException;
use Ixocreate\ServiceManager\ServiceManagerInterface;
use Ixocreate\ServiceManager\SubManagerAwareInterface;
use Laminas\Di\Definition\DefinitionInterface;
use Laminas\Di\Resolver\DependencyResolverInterface;
use Laminas\Di\Resolver\InjectionInterface;
use Laminas\Di\Resolver\ValueInjection;
use Psr\Container\ContainerInterface;

final class DependencyResolver implements DependencyResolverInterface
{
    /**
     * @var DefinitionInterface
     */
    protected $definition;

    /**
     * @var ServiceManagerInterface|null
     */
    protected $container;

    /**
     * DependencyResolver constructor.
     *
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
        if (!$container instanceof ServiceManagerInterface) {
            throw new InvalidArgumentException(\sprintf('Container must implement %s', ServiceManagerInterface::class));
        }
        $this->container = $container;
        return $this;
    }

    /**
     * @param string $requestedType
     * @param array $callTimeParameters
     * @throws \Exception
     * @throws \Psr\Container\NotFoundExceptionInterface
     * @throws \Psr\Container\ContainerExceptionInterface
     * @return InjectionInterface[]
     */
    public function resolveParameters(string $requestedType, array $callTimeParameters = []): array
    {
        if ($this->container === null) {
            throw new \Exception('Container must be set before calling method');
        }

        $subManagerServiceNames = [];
        if ($this->container->serviceManagerConfig() instanceof SubManagerAwareInterface) {
            $subManagerServiceNames = \array_keys($this->container->serviceManagerConfig()->getSubManagers());
        }

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

                foreach ($subManagerServiceNames as $serviceName) {
                    $subManager = $this->container->get($serviceName);

                    if ($subManager->has($type)) {
                        $result[$name] = new ContainerInjection($type, $serviceName);
                        continue 2;
                    }

                    // find dependency by param name
                    if ($subManager->has($name) && $type === $subManager->getValidation()) {
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

        return $result;
    }

    /**
     * @param string $type
     * @param null|string $context
     * @return null|string
     */
    public function resolvePreference(string $type, ?string $context = null): ?string
    {
        return null;
    }
}
