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
namespace KiwiSuiteTest\ServiceManager\Autowire\FactoryResolver;

use KiwiSuite\ServiceManager\Autowire\DependencyResolver;
use KiwiSuite\ServiceManager\Autowire\FactoryCode;
use KiwiSuite\ServiceManager\Autowire\FactoryResolver\RuntimeFactoryResolver;
use KiwiSuite\ServiceManager\ServiceManager;
use KiwiSuite\ServiceManager\ServiceManagerConfig;
use KiwiSuite\ServiceManager\ServiceManagerSetup;
use KiwiSuiteMisc\ServiceManager\ResolverTestObjectNoConstructor;
use PHPUnit\Framework\TestCase;
use Zend\Di\Definition\RuntimeDefinition;

class RuntimeFactoryResolverTest extends TestCase
{
    public function testGetFactory()
    {
        $requestedName = ResolverTestObjectNoConstructor::class;
        $factoryCode = new FactoryCode();
        $resolver = new DependencyResolver(new RuntimeDefinition());
        $resolver->setContainer(new ServiceManager(new ServiceManagerConfig([]), new ServiceManagerSetup()));

        $runtimeFactoryResolver = new RuntimeFactoryResolver($resolver, $factoryCode);


        $this->assertInstanceOf($factoryCode->generateFactoryFullQualifiedName($requestedName), $runtimeFactoryResolver->getFactory($requestedName));
        $this->assertInstanceOf($factoryCode->generateFactoryFullQualifiedName($requestedName), $runtimeFactoryResolver->getFactory($requestedName));
    }
}
