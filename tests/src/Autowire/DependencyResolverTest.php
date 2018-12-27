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
namespace IxocreateTest\ServiceManager\Autowire;

use Ixocreate\ServiceManager\Autowire\ContainerInjection;
use Ixocreate\ServiceManager\Autowire\DefaultValueInjection;
use Ixocreate\ServiceManager\Autowire\DependencyResolver;
use Ixocreate\ServiceManager\Factory\AutowireFactory;
use Ixocreate\ServiceManager\ServiceManager;
use Ixocreate\ServiceManager\ServiceManagerConfig;
use Ixocreate\ServiceManager\ServiceManagerSetup;
use IxocreateMisc\ServiceManager\ComplexObject;
use IxocreateMisc\ServiceManager\DateTimeFactory;
use IxocreateMisc\ServiceManager\DefaultParamObject;
use IxocreateMisc\ServiceManager\OwnDateTime;
use IxocreateMisc\ServiceManager\ResolverTestObject;
use IxocreateMisc\ServiceManager\SubManagerFactory;
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

        $serviceManagerConfig = new ServiceManagerConfig(
            [
                \DateTime::class => DateTimeFactory::class,
                'someThing' => DateTimeFactory::class,
                ResolverTestObject::class => AutowireFactory::class,
                'value2' => AutowireFactory::class,
                DefaultParamObject::class => AutowireFactory::class,
            ],
            [
                'subManager1' => SubManagerFactory::class,
            ]
        );

        $this->serviceManager = new ServiceManager($serviceManagerConfig, new ServiceManagerSetup());
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
        $this->assertSame('subManager1', $resolutions["dateTime"]->getContainer());
        $this->assertSame("dateTime", $resolutions["dateTime"]->getParameterName());

        $this->assertArrayHasKey("value3", $resolutions);
        $this->assertInstanceOf(ContainerInjection::class, $resolutions["value3"]);
        $this->assertSame("value3", $resolutions["value3"]->getType());
        $this->assertSame('subManager1', $resolutions["value3"]->getContainer());
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
