<?php
/**
 * @link https://github.com/ixocreate
 * @copyright IXOCREATE GmbH
 * @license MIT License
 */

declare(strict_types=1);

namespace IxocreateTest\ServiceManager\Autowire\FactoryResolver;

use Ixocreate\ServiceManager\Autowire\DependencyResolver;
use Ixocreate\ServiceManager\Autowire\FactoryCode;
use Ixocreate\ServiceManager\Autowire\FactoryResolver\RuntimeFactoryResolver;
use Ixocreate\ServiceManager\ServiceManager;
use Ixocreate\ServiceManager\ServiceManagerConfig;
use Ixocreate\ServiceManager\ServiceManagerConfigurator;
use Ixocreate\ServiceManager\ServiceManagerSetup;
use IxocreateMisc\ServiceManager\ResolverTestObjectNoConstructor;
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
