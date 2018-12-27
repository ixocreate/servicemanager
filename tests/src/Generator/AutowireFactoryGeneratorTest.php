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
namespace IxocreateTest\ServiceManager\Generator;

use Ixocreate\ServiceManager\Autowire\FactoryCode;
use Ixocreate\ServiceManager\Factory\AutowireFactory;
use Ixocreate\ServiceManager\Generator\AutowireFactoryGenerator;
use Ixocreate\ServiceManager\ServiceManager;
use Ixocreate\ServiceManager\ServiceManagerConfig;
use Ixocreate\ServiceManager\ServiceManagerSetup;
use IxocreateMisc\ServiceManager\DateTimeFactory;
use IxocreateMisc\ServiceManager\ResolverTestObject;
use IxocreateMisc\ServiceManager\SubManagerFactory;
use IxocreateTest\ServiceManager\CleanUpTrait;
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
        $serviceManagerConfig = new ServiceManagerConfig(
            [
                \DateTime::class => DateTimeFactory::class,
                'someThing' => DateTimeFactory::class,
                ResolverTestObject::class => AutowireFactory::class,
            ],
            [
                'subManager1' => SubManagerFactory::class,
            ]
        );

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
