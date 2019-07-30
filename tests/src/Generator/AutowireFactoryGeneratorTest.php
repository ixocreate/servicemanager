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
use Ixocreate\Misc\ServiceManager\SubManagerFactory;
use Ixocreate\ServiceManager\Autowire\FactoryCode;
use Ixocreate\ServiceManager\Factory\AutowireFactory;
use Ixocreate\ServiceManager\Generator\AutowireFactoryGenerator;
use Ixocreate\ServiceManager\ServiceManager;
use Ixocreate\ServiceManager\ServiceManagerConfigInterface;
use Ixocreate\ServiceManager\ServiceManagerSetup;
use Ixocreate\ServiceManager\SubManager\SubManager;
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
            SubManager::class => SubManagerFactory::class,
        ];

        $serviceManagerConfig = $this->createMock(ServiceManagerConfigInterface::class);
        $serviceManagerConfig
            ->method('getFactories')
            ->willReturn($factories);

        $serviceManagerConfig
            ->method('getSubManagers')
            ->willReturn($subManagers);

        $serviceManagerConfig
            ->method('getConfig')
            ->willReturn([
                'factories' => \array_merge($factories, $subManagers),
                'delegators' => [],
                'initializers' => [],
                'shared_by_default' => true,
            ]);

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
        $this->assertFileExists($this->serviceManager->getServiceManagerSetup()->getAutowireLocation() . $factoryCode->generateFactoryName(ResolverTestObject::class) . '.php');
    }
}
