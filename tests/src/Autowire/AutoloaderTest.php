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
namespace KiwiSuiteTest\ServiceManager\Autowire;

use KiwiSuite\ServiceManager\Autowire\Autoloader;
use KiwiSuite\ServiceManager\Autowire\FactoryCode;
use KiwiSuite\ServiceManager\Factory\AutowireFactory;
use KiwiSuite\ServiceManager\FactoryInterface;
use KiwiSuite\ServiceManager\Generator\AutowireFactoryGenerator;
use KiwiSuite\ServiceManager\ServiceManager;
use KiwiSuite\ServiceManager\ServiceManagerConfig;
use KiwiSuite\ServiceManager\ServiceManagerSetup;
use KiwiSuiteMisc\ServiceManager\DateTimeFactory;
use KiwiSuiteMisc\ServiceManager\ResolverTestObject;
use KiwiSuiteMisc\ServiceManager\ResolverTestObjectNoConstructor;
use KiwiSuiteMisc\ServiceManager\SubManagerFactory;
use KiwiSuiteTest\ServiceManager\CleanUpTrait;
use PHPUnit\Framework\TestCase;

class AutoloaderTest extends TestCase
{
    use CleanUpTrait;
    /**
     * @var ServiceManager
     */
    private $serviceManager;

    public function setUp()
    {
        $serviceManagerConfig = new ServiceManagerConfig([
            'factories' => [
                \DateTime::class => DateTimeFactory::class,
                'someThing' => DateTimeFactory::class,
                ResolverTestObject::class => AutowireFactory::class,
            ],
            'subManagers' => [
                'subManager1' => SubManagerFactory::class,
            ],
        ]);

        $this->serviceManager = new ServiceManager($serviceManagerConfig, new ServiceManagerSetup('resources/generated_autoload/servicemanger/', null, true));
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testAutoload()
    {
        $generator = new AutowireFactoryGenerator();
        $generator->generate($this->serviceManager);

        $this->serviceManager->getFactoryResolver();

        $factoryCode = new FactoryCode();
        $className = $factoryCode->generateFactoryFullQualifiedName(ResolverTestObject::class);

        $this->assertInstanceOf(FactoryInterface::class, new $className());
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testClassExists()
    {
        $generator = new AutowireFactoryGenerator();
        $generator->generate($this->serviceManager);

        $autoload = new Autoloader($this->serviceManager);
        $this->assertFalse($autoload->__invoke(ResolverTestObject::class));
    }

    public function testNotAutowireClass()
    {
        $autoload = new Autoloader($this->serviceManager);

        $this->assertFalse($autoload->__invoke(ResolverTestObjectNoConstructor::class));
    }

    public function testFileDontExists()
    {
        $autoload = new Autoloader($this->serviceManager);

        $this->assertFalse($autoload->__invoke('KiwiSuite\\GeneratedFactory\\FactoryFooBar'));
    }
}
