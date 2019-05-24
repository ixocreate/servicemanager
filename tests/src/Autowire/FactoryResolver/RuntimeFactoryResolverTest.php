<?php
/**
 * @link https://github.com/ixocreate
 * @copyright IXOLIT GmbH
 * @license MIT License
 */

declare(strict_types=1);

namespace Ixocreate\Test\ServiceManager\Autowire\FactoryResolver;

use Ixocreate\Misc\ServiceManager\ResolverTestObjectNoConstructor;
use Ixocreate\ServiceManager\Autowire\DependencyResolver;
use Ixocreate\ServiceManager\Autowire\FactoryCode;
use Ixocreate\ServiceManager\Autowire\FactoryResolver\RuntimeFactoryResolver;
use Ixocreate\ServiceManager\ServiceManager;
use Ixocreate\ServiceManager\ServiceManagerConfigInterface;
use Ixocreate\ServiceManager\ServiceManagerSetup;
use PHPUnit\Framework\TestCase;
use Zend\Di\Definition\RuntimeDefinition;

class RuntimeFactoryResolverTest extends TestCase
{
    public function testGetFactory()
    {
        $requestedName = ResolverTestObjectNoConstructor::class;
        $factoryCode = new FactoryCode();
        $resolver = new DependencyResolver(new RuntimeDefinition());
        $resolver->setContainer(new ServiceManager(
            $this->createMock(ServiceManagerConfigInterface::class),
            new ServiceManagerSetup()
        ));

        $runtimeFactoryResolver = new RuntimeFactoryResolver($resolver, $factoryCode);


        $this->assertInstanceOf(
            $factoryCode->generateFactoryFullQualifiedName($requestedName),
            $runtimeFactoryResolver->getFactory($requestedName)
        );
        $this->assertInstanceOf(
            $factoryCode->generateFactoryFullQualifiedName($requestedName),
            $runtimeFactoryResolver->getFactory($requestedName)
        );
    }
}
