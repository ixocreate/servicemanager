<?php
/**
 * kiwi-suite/servicemanager (https://github.com/kiwi-suite/servicemanager)
 *
 * @package kiwi-suite/servicemanager
 * @see https://github.com/kiwi-suite/servicemanager
 * @copyright Copyright (c) 2010 - 2017 kiwi suite GmbH
 * @license MIT License
 */

declare(strict_types=1);
namespace KiwiSuite\ServiceManager\Factory;

use KiwiSuite\ServiceManager\AutowireFactoryInterface;
use KiwiSuite\ServiceManager\Resolver\ReflectionResolver;
use KiwiSuite\ServiceManager\Resolver\Resolution;
use Psr\Container\ContainerInterface;

final class AutowireFactory implements AutowireFactoryInterface
{

    /**
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return mixed
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $resolver = new ReflectionResolver();
        return $resolver->createInstance($container, $this->getResolution($container, $requestedName, $options));
    }

    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return mixed
     */
    public function getResolution(ContainerInterface $container, string $requestedName, array $options = null): Resolution
    {
        $resolver = new ReflectionResolver();
        return $resolver->resolveService($container, $requestedName);
    }
}
