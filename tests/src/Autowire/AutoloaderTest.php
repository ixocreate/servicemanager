<?php
/**
 * @link https://github.com/ixocreate
 * @copyright IXOCREATE GmbH
 * @license MIT License
 */

declare(strict_types=1);

namespace IxocreateTest\ServiceManager\Autowire;

use Ixocreate\Contract\ServiceManager\FactoryInterface;
use Ixocreate\ServiceManager\Autowire\Autoloader;
use Ixocreate\ServiceManager\Autowire\FactoryCode;
use Ixocreate\ServiceManager\Factory\AutowireFactory;
use Ixocreate\ServiceManager\Generator\AutowireFactoryGenerator;
use Ixocreate\ServiceManager\ServiceManager;
use Ixocreate\ServiceManager\ServiceManagerConfig;
use Ixocreate\ServiceManager\ServiceManagerSetup;
use IxocreateMisc\ServiceManager\DateTimeFactory;
use IxocreateMisc\ServiceManager\ResolverTestObject;
use IxocreateMisc\ServiceManager\ResolverTestObjectNoConstructor;
use IxocreateMisc\ServiceManager\SubManagerFactory;
use IxocreateTest\ServiceManager\CleanUpTrait;
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

        $this->assertFalse($autoload->__invoke('Ixocreate\\GeneratedFactory\\FactoryFooBar'));
    }
}
