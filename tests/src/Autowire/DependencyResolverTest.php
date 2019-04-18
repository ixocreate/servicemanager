<?php
/**
 * @link https://github.com/ixocreate
 * @copyright IXOCREATE GmbH
 * @license MIT License
 */

declare(strict_types=1);

namespace Ixocreate\Test\ServiceManager\Autowire;

use Ixocreate\ServiceManager\Autowire\ContainerInjection;
use Ixocreate\ServiceManager\Autowire\DefaultValueInjection;
use Ixocreate\ServiceManager\Autowire\DependencyResolver;
use Ixocreate\ServiceManager\Factory\AutowireFactory;
use Ixocreate\ServiceManager\ServiceManager;
use Ixocreate\ServiceManager\ServiceManagerSetup;
use Ixocreate\ServiceManager\SubManager\SubManager;
use Ixocreate\Misc\ServiceManager\ComplexObject;
use Ixocreate\Misc\ServiceManager\DateTimeFactory;
use Ixocreate\Misc\ServiceManager\DefaultParamObject;
use Ixocreate\Misc\ServiceManager\OwnDateTime;
use Ixocreate\Misc\ServiceManager\ResolverTestObject;
use Ixocreate\Misc\ServiceManager\SubManagerFactory;
use PHPUnit\Framework\TestCase;
use Zend\Di\Definition\RuntimeDefinition;
use Zend\Di\Resolver\ValueInjection;

class DependencyResolverTest extends TestCase
{
    /**
     * @var DependencyResolver
     */
    private $dependencyResolver;

    /**
     * @var ServiceManager
     */
    private $serviceManager;

    public function setUp()
    {
        $this->dependencyResolver = new DependencyResolver(new RuntimeDefinition());

        $serviceManagerConfigurator = new ServiceManagerConfigurator();
        $serviceManagerConfigurator->addService(\DateTime::class, DateTimeFactory::class);
        $serviceManagerConfigurator->addService('someThing', DateTimeFactory::class);
        $serviceManagerConfigurator->addService(ResolverTestObject::class, AutowireFactory::class);
        $serviceManagerConfigurator->addService('value2', AutowireFactory::class);
        $serviceManagerConfigurator->addService(DefaultParamObject::class, AutowireFactory::class);
        $serviceManagerConfigurator->addSubManager(SubManager::class, SubManagerFactory::class);

        $this->serviceManager = new ServiceManager($serviceManagerConfigurator->getServiceManagerConfig(), new ServiceManagerSetup());
        $this->dependencyResolver->setContainer($this->serviceManager);
    }

    public function testSetContainer()
    {
        $this->assertSame($this->dependencyResolver, $this->dependencyResolver->setContainer($this->serviceManager));
    }

    public function testResolveParameters()
    {
        $resolutions = $this->dependencyResolver->resolveParameters(ComplexObject::class, ['value1' => 'test']);

        $this->assertArrayHasKey("value1", $resolutions);
        $this->assertInstanceOf(ValueInjection::class, $resolutions["value1"]);
        $this->assertSame("test", $resolutions["value1"]->getValue());
        $this->assertSame("value1", $resolutions["value1"]->getParameterName());

        $this->assertArrayHasKey("resolverTestObject", $resolutions);
        $this->assertInstanceOf(ContainerInjection::class, $resolutions["resolverTestObject"]);
        $this->assertSame(ResolverTestObject::class, $resolutions["resolverTestObject"]->getType());
        $this->assertSame(null, $resolutions["resolverTestObject"]->getContainer());
        $this->assertSame("resolverTestObject", $resolutions["resolverTestObject"]->getParameterName());

        $this->assertArrayHasKey("value2", $resolutions);
        $this->assertInstanceOf(ContainerInjection::class, $resolutions["value2"]);
        $this->assertSame("value2", $resolutions["value2"]->getType());
        $this->assertSame(null, $resolutions["value2"]->getContainer());
        $this->assertSame("value2", $resolutions["value2"]->getParameterName());

        $this->assertArrayHasKey("dateTime", $resolutions);
        $this->assertInstanceOf(ContainerInjection::class, $resolutions["dateTime"]);
        $this->assertSame(OwnDateTime::class, $resolutions["dateTime"]->getType());
        $this->assertSame(SubManager::class, $resolutions["dateTime"]->getContainer());
        $this->assertSame("dateTime", $resolutions["dateTime"]->getParameterName());

        $this->assertArrayHasKey("value3", $resolutions);
        $this->assertInstanceOf(ContainerInjection::class, $resolutions["value3"]);
        $this->assertSame("value3", $resolutions["value3"]->getType());
        $this->assertSame(SubManager::class, $resolutions["value3"]->getContainer());
        $this->assertSame("value3", $resolutions["value3"]->getParameterName());

        $this->assertArrayHasKey("defaultParamObject", $resolutions);
        $this->assertInstanceOf(ContainerInjection::class, $resolutions["defaultParamObject"]);
        $this->assertSame(DefaultParamObject::class, $resolutions["defaultParamObject"]->getType());
        $this->assertSame(null, $resolutions["defaultParamObject"]->getContainer());
        $this->assertSame("defaultParamObject", $resolutions["defaultParamObject"]->getParameterName());

        $resolutions = $this->dependencyResolver->resolveParameters(DefaultParamObject::class);
        $this->assertArrayHasKey("name", $resolutions);
        $this->assertInstanceOf(DefaultValueInjection::class, $resolutions["name"]);
        $this->assertSame("name", $resolutions["name"]->getValue());
        $this->assertSame("name", $resolutions["name"]->getParameterName());
    }

    public function testResolvePreference()
    {
        $this->assertNull($this->dependencyResolver->resolvePreference("string"));
    }
}
