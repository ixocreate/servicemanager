<?php
/**
 * @link https://github.com/ixocreate
 * @copyright IXOLIT GmbH
 * @license MIT License
 */

declare(strict_types=1);

namespace Ixocreate\Test\ServiceManager\Autowire;

use Ixocreate\Misc\ServiceManager\ComplexObject;
use Ixocreate\Misc\ServiceManager\DateTimeFactory;
use Ixocreate\Misc\ServiceManager\DefaultParamObject;
use Ixocreate\Misc\ServiceManager\OwnDateTime;
use Ixocreate\Misc\ServiceManager\ResolverTestObject;
use Ixocreate\Misc\ServiceManager\ResolverTestObjectNoConstructor;
use Ixocreate\Misc\ServiceManager\ServiceManagerConfig;
use Ixocreate\Misc\ServiceManager\SubManager\DateTimeManagerFactory;
use Ixocreate\ServiceManager\Autowire\ContainerInjection;
use Ixocreate\ServiceManager\Autowire\DefaultValueInjection;
use Ixocreate\ServiceManager\Autowire\DependencyResolver;
use Ixocreate\ServiceManager\Exception\InvalidArgumentException;
use Ixocreate\ServiceManager\Factory\AutowireFactory;
use Ixocreate\ServiceManager\ServiceManager;
use Ixocreate\ServiceManager\ServiceManagerSetup;
use Ixocreate\ServiceManager\SubManager\AbstractSubManager;
use Laminas\Di\Definition\RuntimeDefinition;
use Laminas\Di\Resolver\ValueInjection;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

class DependencyResolverTest extends TestCase
{
    /**
     * @var ServiceManager
     */
    private $serviceManager;

    public function setUp()
    {
        $factories = [
            \DateTime::class => DateTimeFactory::class,
            'someThing' => DateTimeFactory::class,
            ResolverTestObject::class => AutowireFactory::class,
            'value2' => AutowireFactory::class,
            DefaultParamObject::class => AutowireFactory::class,
        ];
        $subManagers = [
            AbstractSubManager::class => DateTimeManagerFactory::class,
        ];

        $serviceManagerConfig = new ServiceManagerConfig($factories, [], [], [], [], $subManagers);

        $this->serviceManager = new ServiceManager(
            $serviceManagerConfig,
            new ServiceManagerSetup()
        );
    }

    /**
     * @covers \Ixocreate\ServiceManager\Autowire\DependencyResolver
     */
    public function testSetContainer()
    {
        $dependencyResolver = new DependencyResolver(new RuntimeDefinition());

        $this->assertSame($dependencyResolver, $dependencyResolver->setContainer($this->serviceManager));
    }

    /**
     * @covers \Ixocreate\ServiceManager\Autowire\DependencyResolver
     */
    public function testInvalidSetContainer()
    {
        $this->expectException(InvalidArgumentException::class);

        $serviceManager = $this->createMock(ContainerInterface::class);

        $dependencyResolver = new DependencyResolver(new RuntimeDefinition());
        $dependencyResolver->setContainer($serviceManager);
    }

    /**
     * @covers \Ixocreate\ServiceManager\Autowire\DependencyResolver
     */
    public function testNoResolveParameters()
    {
        $dependencyResolver = new DependencyResolver(new RuntimeDefinition());
        $dependencyResolver->setContainer($this->serviceManager);

        $resolutions = $dependencyResolver->resolveParameters(ResolverTestObjectNoConstructor::class);

        $this->assertEquals([], $resolutions);
    }

    /**
     * @covers \Ixocreate\ServiceManager\Autowire\DependencyResolver
     */
    public function testResolveParameters()
    {
        $dependencyResolver = new DependencyResolver(new RuntimeDefinition());
        $dependencyResolver->setContainer($this->serviceManager);

        $resolutions = $dependencyResolver->resolveParameters(ComplexObject::class, ['value1' => 'test']);

        $this->assertArrayHasKey('value1', $resolutions);
        $this->assertInstanceOf(ValueInjection::class, $resolutions['value1']);
        $this->assertSame('test', $resolutions['value1']->toValue($this->serviceManager));

        $this->assertArrayHasKey('resolverTestObject', $resolutions);
        $this->assertInstanceOf(ContainerInjection::class, $resolutions['resolverTestObject']);
        $this->assertSame(ResolverTestObject::class, $resolutions['resolverTestObject']->getType());
        $this->assertSame(null, $resolutions['resolverTestObject']->getContainer());

        $this->assertArrayHasKey('value2', $resolutions);
        $this->assertInstanceOf(ContainerInjection::class, $resolutions['value2']);
        $this->assertSame('value2', $resolutions['value2']->getType());
        $this->assertSame(null, $resolutions['value2']->getContainer());

        $this->assertArrayHasKey('dateTime', $resolutions);
        $this->assertInstanceOf(ContainerInjection::class, $resolutions['dateTime']);
        $this->assertSame(OwnDateTime::class, $resolutions['dateTime']->getType());
        $this->assertSame(AbstractSubManager::class, $resolutions['dateTime']->getContainer());

        $this->assertArrayHasKey('value3', $resolutions);
        $this->assertInstanceOf(ContainerInjection::class, $resolutions['value3']);
        $this->assertSame('value3', $resolutions['value3']->getType());
        $this->assertSame(AbstractSubManager::class, $resolutions['value3']->getContainer());

        $this->assertArrayHasKey('defaultParamObject', $resolutions);
        $this->assertInstanceOf(ContainerInjection::class, $resolutions['defaultParamObject']);
        $this->assertSame(DefaultParamObject::class, $resolutions['defaultParamObject']->getType());
        $this->assertSame(null, $resolutions['defaultParamObject']->getContainer());

        $resolutions = $dependencyResolver->resolveParameters(DefaultParamObject::class);
        $this->assertArrayHasKey('name', $resolutions);
        $this->assertInstanceOf(DefaultValueInjection::class, $resolutions['name']);
        $this->assertSame('name', $resolutions['name']->toValue($this->serviceManager));
    }

    /**
     * @covers \Ixocreate\ServiceManager\Autowire\DependencyResolver
     */
    public function testContainerNotSet()
    {
        $this->expectException(\Exception::class);

        $dependencyResolver = new DependencyResolver(new RuntimeDefinition());
        $dependencyResolver->resolveParameters(ResolverTestObjectNoConstructor::class);
    }

    /**
     * @covers \Ixocreate\ServiceManager\Autowire\DependencyResolver
     */
    public function testResolvePreference()
    {
        $dependencyResolver = new DependencyResolver(new RuntimeDefinition());
        $dependencyResolver->setContainer($this->serviceManager);

        $this->assertNull($dependencyResolver->resolvePreference('string'));
    }
}
