<?php
/**
 * @link https://github.com/ixocreate
 * @copyright IXOLIT GmbH
 * @license MIT License
 */

declare(strict_types=1);

namespace Ixocreate\Test\ServiceManager;

use Ixocreate\Misc\ServiceManager\CantCreateObjectFactory;
use Ixocreate\Misc\ServiceManager\DateTimeFactory;
use Ixocreate\Misc\ServiceManager\SubManager\DateTimeManager;
use Ixocreate\Misc\ServiceManager\SubManager\SerializableManager;
use Ixocreate\ServiceManager\Exception\ServiceNotCreatedException;
use Ixocreate\ServiceManager\Exception\ServiceNotFoundException;
use Ixocreate\ServiceManager\ServiceManager;
use Ixocreate\ServiceManager\ServiceManagerConfigInterface;
use Ixocreate\ServiceManager\ServiceManagerSetup;
use Ixocreate\ServiceManager\SubManager\AbstractSubManager;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Ixocreate\ServiceManager\SubManager\AbstractSubManager
 */
class SubManagerTest extends TestCase
{
    /**
     * @var ServiceManager
     */
    private $serviceManager;

    /**
     * @var AbstractSubManager
     */
    private $subManager;

    /**
     * @var ServiceManagerConfigInterface
     */
    private $subManagerConfig;

    public function setUp()
    {
        $this->serviceManager = new ServiceManager(
            $this->createMock(ServiceManagerConfigInterface::class),
            new ServiceManagerSetup()
        );

        $factories = [
            'dateTime' => DateTimeFactory::class,
            'cantCreate' => CantCreateObjectFactory::class,
        ];
        $namedServices = [
            'dateTimeNamed' => 'dateTime'
        ];
        $serviceManagerConfig = $this->createMock(ServiceManagerConfigInterface::class);
        $serviceManagerConfig
            ->method('getFactories')
            ->willReturn($factories);
        $serviceManagerConfig
            ->method('getNamedServices')
            ->willReturn($namedServices);

        $this->subManagerConfig = $serviceManagerConfig;

        $this->subManager = new DateTimeManager(
            $this->serviceManager,
            $this->subManagerConfig
        );
    }

    public function testGet()
    {
        $this->assertInstanceOf(\DateTimeInterface::class, $this->subManager->get('dateTime'));
    }

    public function testBuild()
    {
        $this->assertInstanceOf(\DateTimeInterface::class, $this->subManager->build('dateTime'));
    }

    public function testHas()
    {
        $this->assertTrue($this->subManager->has('dateTime'));
        $this->assertFalse($this->subManager->has('doesnt_exist'));
    }

    public function testServiceNotFoundExceptionGet()
    {
        $this->expectException(ServiceNotFoundException::class);

        $this->subManager->get('doesnt_exists');
    }

    public function testServiceNotFoundExceptionBuild()
    {
        $this->expectException(ServiceNotFoundException::class);

        $this->subManager->build('doesnt_exists');
    }

    public function testServiceNotCreatedExceptionGet()
    {
        $this->expectException(ServiceNotCreatedException::class);

        $this->subManager->get('cantCreate');
    }

    public function testServiceNotCreatedExceptionBuild()
    {
        $this->expectException(ServiceNotCreatedException::class);

        $this->subManager->build('cantCreate');
    }

    public function testGetValidation()
    {
        $this->assertEquals(\DateTimeInterface::class, $this->subManager->getValidation());
    }

    public function testGetServiceManagerSetup()
    {
        $this->assertEquals(
            $this->serviceManager->serviceManagerSetup(),
            $this->subManager->serviceManagerSetup()
        );
    }

    public function testGetServiceManagerConfig()
    {
        $this->assertEquals($this->subManagerConfig, $this->subManager->serviceManagerConfig());
    }

    public function testInvalidateGet()
    {
        $this->expectException(ServiceNotCreatedException::class);

        $factories = [
            'test' => DateTimeFactory::class,
        ];
        $serviceManagerConfig = $this->createMock(ServiceManagerConfigInterface::class);
        $serviceManagerConfig
            ->method('getFactories')
            ->willReturn($factories);

        $serviceManager = new SerializableManager(
            $this->serviceManager,
            $serviceManagerConfig
        );

        $serviceManager->get('test');
    }

    public function testInvalidateBuild()
    {
        $this->expectException(ServiceNotCreatedException::class);

        $factories = [
            'test' => DateTimeFactory::class,
        ];
        $serviceManagerConfig = $this->createMock(ServiceManagerConfigInterface::class);
        $serviceManagerConfig
            ->method('getFactories')
            ->willReturn($factories);

        $serviceManager = new SerializableManager(
            $this->serviceManager,
            $serviceManagerConfig
        );

        $serviceManager->build('test');
    }

    public function testNamedService()
    {
        $namedDate = $this->subManager->get('dateTimeNamed');
        $date = $this->subManager->get('dateTime');

        $this->assertSame($date, $namedDate);
    }

    public function testGetFactoryResolver()
    {
        $this->assertSame($this->serviceManager->factoryResolver(), $this->subManager->factoryResolver());
    }

    public function testServices()
    {
        $serviceManager = new SerializableManager($this->serviceManager, $this->subManagerConfig);

        $servicerNames = [
            'dateTime',
            'cantCreate',
        ];

        $this->assertEquals($servicerNames, $serviceManager->services());
    }

    public function testInitialServices()
    {
        $services = [
            \DateTime::class => new \DateTime(),
        ];

        $subManager = new DateTimeManager(
            $this->serviceManager,
            $this->subManagerConfig,
            $services
        );

        $this->assertSame($services, $subManager->initialServices());
    }
}
