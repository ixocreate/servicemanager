<?php
/**
 * @link https://github.com/ixocreate
 * @copyright IXOCREATE GmbH
 * @license MIT License
 */

declare(strict_types=1);

namespace Ixocreate\Test\ServiceManager\Generator;

use Ixocreate\ServiceManager\Autowire\FactoryCode;
use Ixocreate\ServiceManager\Factory\AutowireFactory;
use Ixocreate\ServiceManager\Generator\AutowireFactoryGenerator;
use Ixocreate\ServiceManager\ServiceManager;
use Ixocreate\ServiceManager\ServiceManagerConfigurator;
use Ixocreate\ServiceManager\ServiceManagerSetup;
use Ixocreate\ServiceManager\SubManager\SubManager;
use Ixocreate\Misc\ServiceManager\DateTimeFactory;
use Ixocreate\Misc\ServiceManager\ResolverTestObject;
use Ixocreate\Misc\ServiceManager\SubManagerFactory;
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
        $serviceManagerConfigurator = new ServiceManagerConfigurator();
        $serviceManagerConfigurator->addService(\DateTime::class, DateTimeFactory::class);
        $serviceManagerConfigurator->addService('someThing', DateTimeFactory::class);
        $serviceManagerConfigurator->addService(ResolverTestObject::class, AutowireFactory::class);
        $serviceManagerConfigurator->addSubManager(SubManager::class, SubManagerFactory::class);

        $this->serviceManager = new ServiceManager($serviceManagerConfigurator->getServiceManagerConfig(), new ServiceManagerSetup());
    }

    public function testGenerate()
    {
        $generator = new AutowireFactoryGenerator();
        $generator->generate($this->serviceManager);

        $factoryCode = new FactoryCode();
        $this->assertFileExists($this->serviceManager->getServiceManagerSetup()->getAutowireLocation() . $factoryCode->generateFactoryName(ResolverTestObject::class) . '.php');
    }
}
