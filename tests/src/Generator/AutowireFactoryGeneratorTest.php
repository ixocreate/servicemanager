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
namespace KiwiSuiteTest\ServiceManager\Generator;

use KiwiSuite\ServiceManager\Autowire\FactoryCode;
use KiwiSuite\ServiceManager\Factory\AutowireFactory;
use KiwiSuite\ServiceManager\Generator\AutowireFactoryGenerator;
use KiwiSuite\ServiceManager\ServiceManager;
use KiwiSuite\ServiceManager\ServiceManagerConfig;
use KiwiSuite\ServiceManager\ServiceManagerSetup;
use KiwiSuiteMisc\ServiceManager\DateTimeFactory;
use KiwiSuiteMisc\ServiceManager\ResolverTestObject;
use KiwiSuiteMisc\ServiceManager\SubManagerFactory;
use KiwiSuiteTest\ServiceManager\CleanUpTrait;
use PHPUnit\Framework\TestCase;

class AutowireFactoryGeneratorTest extends TestCase
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

        $this->serviceManager = new ServiceManager($serviceManagerConfig, new ServiceManagerSetup());
    }

    public function testGenerate()
    {
        $generator = new AutowireFactoryGenerator();
        $generator->generate($this->serviceManager);

        $factoryCode = new FactoryCode();
        $this->assertFileExists($this->serviceManager->getServiceManagerSetup()->getAutowireLocation() . $factoryCode->generateFactoryName(ResolverTestObject::class) . '.php');
    }
}
