<?php
/**
 * @link https://github.com/ixocreate
 * @copyright IXOCREATE GmbH
 * @license MIT License
 */

declare(strict_types=1);

namespace Ixocreate\Test\ServiceManager\Autowire\FactoryResolver;

use Ixocreate\ServiceManager\Autowire\DependencyResolver;
use Ixocreate\ServiceManager\Autowire\FactoryCode;
use Ixocreate\ServiceManager\Autowire\FactoryResolver\RuntimeFactoryResolver;
use Ixocreate\ServiceManager\ServiceManager;
use Ixocreate\ServiceManager\ServiceManagerSetup;
use Ixocreate\Misc\ServiceManager\ResolverTestObjectNoConstructor;
use PHPUnit\Framework\TestCase;
use Zend\Di\Definition\RuntimeDefinition;

class RuntimeFactoryResolverTest extends TestCase
{
    public function testGetFactory()
    {
        $requestedName = ResolverTestObjectNoConstructor::class;
        $factoryCode = new FactoryCode();
        $resolver = new DependencyResolver(new RuntimeDefinition());
        $resolver->setContainer(new ServiceManager(new ServiceManagerConfig(new ServiceManagerConfigurator()), new ServiceManagerSetup()));

        $runtimeFactoryResolver = new RuntimeFactoryResolver($resolver, $factoryCode);


        $this->assertInstanceOf($factoryCode->generateFactoryFullQualifiedName($requestedName), $runtimeFactoryResolver->getFactory($requestedName));
        $this->assertInstanceOf($factoryCode->generateFactoryFullQualifiedName($requestedName), $runtimeFactoryResolver->getFactory($requestedName));
    }
}
