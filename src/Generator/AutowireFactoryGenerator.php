<?php
/**
 * kiwi-suite/servicemanager (https://github.com/kiwi-suite/servicemanager)
 *
 * @package kiwi-suite/servicemanager
 * @link https://github.com/kiwi-suite/servicemanager
 * @copyright Copyright (c) 2010 - 2018 kiwi suite GmbH
 * @license MIT License
 */

declare(strict_types=1);
namespace Ixocreate\ServiceManager\Generator;

use Ixocreate\Contract\ServiceManager\AutowireFactoryInterface;
use Ixocreate\Contract\ServiceManager\ServiceManagerInterface;
use Ixocreate\ServiceManager\Autowire\DependencyResolver;
use Ixocreate\ServiceManager\Autowire\FactoryCode;
use Zend\Di\Definition\RuntimeDefinition;

final class AutowireFactoryGenerator
{
    public function generate(ServiceManagerInterface $serviceManager): void
    {
        $services = $this->requestAutowireFactories($serviceManager);
        foreach (\array_keys($serviceManager->getServiceManagerConfig()->getSubManagers()) as $subManager) {
            $services = \array_merge($services, $this->requestAutowireFactories($serviceManager->get($subManager)));
        }

        if (!\file_exists($serviceManager->getServiceManagerSetup()->getAutowireLocation())) {
            \mkdir($serviceManager->getServiceManagerSetup()->getAutowireLocation(), 0777, true);
        }

        $factoryCode = new FactoryCode();
        foreach ($services as $service) {
            $code = $this->generateCode($serviceManager, $factoryCode, $service);
            $filename = $serviceManager->getServiceManagerSetup()->getAutowireLocation() . $factoryCode->generateFactoryName($service) . ".php";

            \file_put_contents($filename, $code);
        }
    }

    private function requestAutowireFactories(ServiceManagerInterface $serviceManager): array
    {
        $services = [];
        $serviceManagerConfig = $serviceManager->getServiceManagerConfig();
        foreach ($serviceManagerConfig->getFactories() as $serviceName => $factory) {
            $implements = \class_implements($factory);
            if (!\in_array(AutowireFactoryInterface::class, $implements)) {
                continue;
            }

            $services[] = $serviceName;
        }
        return $services;
    }

    private function generateCode(ServiceManagerInterface $serviceManager, FactoryCode $factoryCode, string $service): string
    {
        $dependencyResolver = new DependencyResolver(new RuntimeDefinition());
        $dependencyResolver->setContainer($serviceManager);

        return $factoryCode->generateFactoryCode($service, $dependencyResolver->resolveParameters($service));
    }
}
