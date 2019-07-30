<?php
/**
 * @link https://github.com/ixocreate
 * @copyright IXOLIT GmbH
 * @license MIT License
 */

declare(strict_types=1);

namespace Ixocreate\ServiceManager\Autowire\FactoryResolver;

use Ixocreate\ServiceManager\Autowire\DependencyResolver;
use Ixocreate\ServiceManager\Autowire\FactoryCode;
use Ixocreate\ServiceManager\Autowire\FactoryResolverInterface;
use Ixocreate\ServiceManager\FactoryInterface;

final class RuntimeFactoryResolver implements FactoryResolverInterface
{
    /**
     * @var FactoryCode
     */
    private $factoryCode;

    /**
     * @var DependencyResolver
     */
    private $dependencyResolver;

    /**
     * RuntimeFactoryResolver constructor.
     *
     * @param DependencyResolver $dependencyResolver
     * @param FactoryCode $factoryCode
     */
    public function __construct(DependencyResolver $dependencyResolver, FactoryCode $factoryCode)
    {
        $this->factoryCode = $factoryCode;
        $this->dependencyResolver = $dependencyResolver;
    }

    /**
     * @param string $requestedName
     * @param array|null $options
     * @throws \Psr\Container\NotFoundExceptionInterface
     * @throws \Psr\Container\ContainerExceptionInterface
     * @return FactoryInterface
     */
    public function getFactory(string $requestedName, array $options = null): FactoryInterface
    {
        $factoryName = $this->factoryCode->generateFactoryFullQualifiedName($requestedName);

        if (\class_exists($factoryName)) {
            return new $factoryName();
        }

        $fileName = \tempnam(\sys_get_temp_dir(), $factoryName . '.php.tmp.');

        if ($options === null) {
            $options = [];
        }

        \file_put_contents(
            $fileName,
            $this->factoryCode->generateFactoryCode(
                $requestedName,
                $this->dependencyResolver->resolveParameters($requestedName, $options)
            )
        );

        /* @noinspection PhpIncludeInspection */
        require $fileName;
        \unlink($fileName);

        return new $factoryName();
    }
}
