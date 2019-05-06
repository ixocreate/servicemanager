<?php
/**
 * @link https://github.com/ixocreate
 * @copyright IXOCREATE GmbH
 * @license MIT License
 */

declare(strict_types=1);

namespace Ixocreate\Test\ServiceManager\Autowire;

use Ixocreate\Misc\ServiceManager\DateTimeFactory;
use Ixocreate\Misc\ServiceManager\ResolverTestObject;
use Ixocreate\Misc\ServiceManager\ResolverTestObjectNoConstructor;
use Ixocreate\Misc\ServiceManager\SubManagerFactory;
use Ixocreate\ServiceManager\Autowire\Autoloader;
use Ixocreate\ServiceManager\Autowire\FactoryCode;
use Ixocreate\ServiceManager\Factory\AutowireFactory;
use Ixocreate\ServiceManager\FactoryInterface;
use Ixocreate\ServiceManager\Generator\AutowireFactoryGenerator;
use Ixocreate\ServiceManager\ServiceManager;
use Ixocreate\ServiceManager\ServiceManagerConfigInterface;
use Ixocreate\ServiceManager\ServiceManagerSetup;
use Ixocreate\ServiceManager\SubManager\SubManager;
use Ixocreate\Test\ServiceManager\CleanUpTrait;
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
            new ServiceManagerSetup('resources/generated_autoload/servicemanger/', null, true)
        );
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

        $this->assertFalse($autoload->__invoke('Ixocreate\\GeneratedFactory\\FactoryFooBar'));
    }
}
