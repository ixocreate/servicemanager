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
namespace KiwiSuite\ServiceManager\Autowire\FactoryResolver;

use KiwiSuite\Contract\ServiceManager\Autowire\FactoryResolverInterface;
use KiwiSuite\Contract\ServiceManager\FactoryInterface;
use KiwiSuite\ServiceManager\Autowire\DependencyResolver;
use KiwiSuite\ServiceManager\Autowire\FactoryCode;

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
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
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
