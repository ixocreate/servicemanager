<?php
/**
 * @link https://github.com/ixocreate
 * @copyright IXOLIT GmbH
 * @license MIT License
 */

declare(strict_types=1);

namespace Ixocreate\Test\ServiceManager\Generator;

use Ixocreate\Misc\ServiceManager\DateTimeFactory;
use Ixocreate\Misc\ServiceManager\ResolverTestObject;
use Ixocreate\Misc\ServiceManager\ServiceManagerConfig;
use Ixocreate\Misc\ServiceManager\SubManager\DateTimeManagerFactory;
use Ixocreate\ServiceManager\Autowire\FactoryCode;
use Ixocreate\ServiceManager\Factory\AutowireFactory;
use Ixocreate\ServiceManager\Generator\AutowireFactoryGenerator;
use Ixocreate\ServiceManager\ServiceManager;
use Ixocreate\ServiceManager\ServiceManagerSetup;
use Ixocreate\ServiceManager\SubManager\AbstractSubManager;
use Ixocreate\Test\ServiceManager\CleanUpTrait;
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
        $factories = [
            \DateTime::class => DateTimeFactory::class,
            'someThing' => DateTimeFactory::class,
            ResolverTestObject::class => AutowireFactory::class,
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

    public function testGenerate()
    {
        $generator = new AutowireFactoryGenerator();
        $generator->generate($this->serviceManager);

        $factoryCode = new FactoryCode();
        $this->assertFileExists($this->serviceManager->serviceManagerSetup()->getAutowireLocation() . $factoryCode->generateFactoryName(ResolverTestObject::class) . '.php');
    }
}
