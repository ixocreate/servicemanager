<?php
/**
 * @link https://github.com/ixocreate
 * @copyright IXOLIT GmbH
 * @license MIT License
 */

declare(strict_types=1);

namespace Ixocreate\Test\ServiceManager\Autowire;

use Ixocreate\Misc\ServiceManager\DateTimeFactory;
use Ixocreate\Misc\ServiceManager\FactoryGeneratorTestObject;
use Ixocreate\Misc\ServiceManager\ResolverTestObject;
use Ixocreate\Misc\ServiceManager\ServiceManagerConfig;
use Ixocreate\Misc\ServiceManager\SubManager\DateTimeManagerFactory;
use Ixocreate\ServiceManager\Autowire\ContainerInjection;
use Ixocreate\ServiceManager\Autowire\DefaultValueInjection;
use Ixocreate\ServiceManager\Autowire\FactoryCode;
use Ixocreate\ServiceManager\Factory\AutowireFactory;
use Ixocreate\ServiceManager\ServiceManager;
use Ixocreate\ServiceManager\ServiceManagerSetup;
use Ixocreate\ServiceManager\SubManager\AbstractSubManager;
use Laminas\Di\Resolver\ValueInjection;
use PHPUnit\Framework\TestCase;

/**
 * Class FactoryCodeTest
 * @package Ixocreate\Test\ServiceManager\Autowire
 * @covers \Ixocreate\ServiceManager\Autowire\FactoryCode
 */
class FactoryCodeTest extends TestCase
{
    /**
     * @var FactoryCode
     */
    private $factoryCode;

    /**
     * @var ServiceManager
     */
    private $serviceManager;

    public function setUp(): void
    {
        $this->factoryCode = new FactoryCode();

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

    public function testGenerateFactoryName()
    {
        $requestedName = \DateTime::class;

        $this->assertSame('Factory' . \md5($requestedName), $this->factoryCode->generateFactoryName($requestedName));
    }

    public function testGenerateFactoryFullQualifiedName()
    {
        $requestedName = \DateTime::class;

        $this->assertSame(
            '\\Ixocreate\\GeneratedFactory\\Factory' . \md5($requestedName),
            $this->factoryCode->generateFactoryFullQualifiedName($requestedName)
        );
    }

    public function testGenerateCode()
    {
        $resolution = [
            'dateTime' => new ContainerInjection(\DateTime::class, null),
            'test' => new ValueInjection('test'),
            'test1' => new ContainerInjection('test1', AbstractSubManager::class),
            'default1' => new DefaultValueInjection('default'),
            'default2' => new DefaultValueInjection(null),
        ];

        $requestedName = FactoryGeneratorTestObject::class;
        $factoryName = $this->factoryCode->generateFactoryFullQualifiedName($requestedName);

        $code = $this->factoryCode->generateFactoryCode($requestedName, $resolution);

        $fileName = \tempnam(
            \sys_get_temp_dir(),
            $this->factoryCode->generateFactoryName($requestedName) . '.php.tmp.'
        );

        \file_put_contents(
            $fileName,
            $code
        );

        /* @noinspection PhpIncludeInspection */
        require $fileName;
        \unlink($fileName);

        $factory = new $factoryName();

        $object = $factory->__invoke($this->serviceManager, $requestedName, ['test' => 'somestring']);

        $this->assertInstanceOf($requestedName, $object);
        $this->assertSame('somestring', $object->getTest());
        $this->assertSame('default', $object->getDefault1());
        $this->assertNull($object->getDefault2());
        $this->assertInstanceOf(\DateTime::class, $object->getDateTime());
        $this->assertInstanceOf(\DateTime::class, $object->getTest1());
    }
}
