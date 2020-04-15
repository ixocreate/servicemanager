<?php
/**
 * @link https://github.com/ixocreate
 * @copyright IXOLIT GmbH
 * @license MIT License
 */

declare(strict_types=1);

namespace Ixocreate\Test\ServiceManager;

use Ixocreate\Misc\ServiceManager\DateTimeDelegatorFactory;
use Ixocreate\Misc\ServiceManager\DateTimeFactory;
use Ixocreate\Misc\ServiceManager\DateTimeInitializer;
use Ixocreate\Misc\ServiceManager\LazyLoadingObject;
use Ixocreate\Misc\ServiceManager\ServiceManagerConfig;
use Ixocreate\Misc\ServiceManager\SubManager\DateTimeManager;
use Ixocreate\Misc\ServiceManager\SubManager\DateTimeManagerFactory;
use Ixocreate\Misc\ServiceManager\TestInterface;
use Ixocreate\ServiceManager\Factory\AutowireFactory;
use Ixocreate\ServiceManager\OriginalServiceManager;
use Ixocreate\ServiceManager\ServiceManager;
use Ixocreate\ServiceManager\ServiceManagerInterface;
use Ixocreate\ServiceManager\ServiceManagerSetup;
use Laminas\ServiceManager\Proxy\LazyServiceFactory;
use PHPUnit\Framework\TestCase;

/**
 * Class OriginalServiceManagerTest
 * @package Ixocreate\Test\ServiceManager
 * @covers \Ixocreate\ServiceManager\OriginalServiceManager
 */
class OriginalServiceManagerTest extends TestCase
{
    public function testCreationContext()
    {
        $serviceManager = $this->createMock(ServiceManagerInterface::class);

        $factories = [
            'dateTime' => DateTimeFactory::class,
        ];

        $serviceManagerConfig = new ServiceManagerConfig($factories);
        $serviceManagerSetup = new ServiceManagerSetup();

        $originalServiceManager = new OriginalServiceManager($serviceManager, $serviceManagerConfig, $serviceManagerSetup);

        $originalServiceManager->get('dateTime');

        $this->assertSame($serviceManager, DateTimeFactory::lastContainer());
    }

    public function testFactories()
    {
        $serviceManager = $this->createMock(ServiceManagerInterface::class);

        $factories = [
            'dateTime' => DateTimeFactory::class,
            'dateTime2' => DateTimeFactory::class,
        ];

        $serviceManagerConfig = new ServiceManagerConfig($factories);
        $serviceManagerSetup = new ServiceManagerSetup();

        $originalServiceManager = new OriginalServiceManager($serviceManager, $serviceManagerConfig, $serviceManagerSetup);

        $this->assertInstanceOf(\DateTimeInterface::class, $originalServiceManager->get('dateTime'));
        $this->assertInstanceOf(\DateTimeInterface::class, $originalServiceManager->get('dateTime2'));
    }

    public function testInstanceSharing()
    {
        $serviceManager = $this->createMock(ServiceManagerInterface::class);

        $factories = [
            'dateTime' => DateTimeFactory::class,
        ];

        $serviceManagerConfig = new ServiceManagerConfig($factories);
        $serviceManagerSetup = new ServiceManagerSetup();

        $originalServiceManager = new OriginalServiceManager($serviceManager, $serviceManagerConfig, $serviceManagerSetup);

        $this->assertSame($originalServiceManager->get('dateTime'), $originalServiceManager->get('dateTime'));
    }

    public function testSubManagers()
    {
        $serviceManager = $this->createMock(ServiceManagerInterface::class);

        $factories = [
            'dateTime' => DateTimeFactory::class,
        ];
        $subManagers = [
            DateTimeManager::class => DateTimeManagerFactory::class,
        ];

        $serviceManagerConfig = new ServiceManagerConfig($factories, [], [], [], [], $subManagers);
        $serviceManagerSetup = new ServiceManagerSetup();

        $originalServiceManager = new OriginalServiceManager($serviceManager, $serviceManagerConfig, $serviceManagerSetup);

        $this->assertInstanceOf(\DateTimeInterface::class, $originalServiceManager->get('dateTime'));
        $this->assertInstanceOf(DateTimeManager::class, $originalServiceManager->get(DateTimeManager::class));
    }

    public function testDelegator()
    {
        $serviceManager = $this->createMock(ServiceManagerInterface::class);

        $factories = [
            'dateTime' => DateTimeFactory::class,
        ];
        $delegators = [
            'dateTime' => [
                DateTimeDelegatorFactory::class,
            ],
        ];

        $serviceManagerConfig = new ServiceManagerConfig($factories, $delegators);
        $serviceManagerSetup = new ServiceManagerSetup();

        $originalServiceManager = new OriginalServiceManager($serviceManager, $serviceManagerConfig, $serviceManagerSetup);

        $this->assertSame('2000-06-15 12:00:00', $originalServiceManager->get('dateTime')->format('Y-m-d H:i:s'));
    }

    public function testInitializer()
    {
        $serviceManager = $this->createMock(ServiceManagerInterface::class);

        $factories = [
            'dateTime' => DateTimeFactory::class,
        ];
        $initializers = [
            DateTimeInitializer::class,
        ];

        $serviceManagerConfig = new ServiceManagerConfig($factories, [], $initializers);
        $serviceManagerSetup = new ServiceManagerSetup();

        $originalServiceManager = new OriginalServiceManager($serviceManager, $serviceManagerConfig, $serviceManagerSetup);

        $this->assertSame('2000-06-15 12:00:00', $originalServiceManager->get('dateTime')->format('Y-m-d H:i:s'));
    }

    /**
     * @runInSeparateProcess
     */
    public function testLazyLoading()
    {
        $serviceManagerSetup = new ServiceManagerSetup();

        $factories = [
            LazyLoadingObject::class => AutowireFactory::class,
        ];
        $delegators = [
            LazyLoadingObject::class => [
                LazyServiceFactory::class,
            ],
        ];
        $lazyServices = [
            LazyLoadingObject::class => LazyLoadingObject::class,
        ];

        $serviceManagerConfig = new ServiceManagerConfig($factories, $delegators, [], $lazyServices);

        $serviceManager = new ServiceManager($serviceManagerConfig, $serviceManagerSetup);
        $originalServiceManager = new OriginalServiceManager($serviceManager, $serviceManagerConfig, $serviceManagerSetup);

        $result = $originalServiceManager->get(LazyLoadingObject::class);

        $this->assertInstanceOf(LazyLoadingObject::class, $result);
        $this->assertTrue(\in_array(TestInterface::class, \class_implements($result)));
        $this->expectException(\Exception::class);
        $this->expectExceptionCode(500);
        $result->doSomething();
    }

    /**
     * @runInSeparateProcess
     */
    public function testPersistentLazyLoading()
    {
        $persistRoot = \getcwd() . '/tests/tmp';
        $serviceManagerSetup = new ServiceManagerSetup($persistRoot, true);

        $factories = [
            LazyLoadingObject::class => AutowireFactory::class,
        ];
        $delegators = [
            LazyLoadingObject::class => [
                LazyServiceFactory::class,
            ],
        ];
        $lazyServices = [
            LazyLoadingObject::class => LazyLoadingObject::class,
        ];

        $serviceManagerConfig = new ServiceManagerConfig($factories, $delegators, [], $lazyServices);

        $serviceManager = new ServiceManager($serviceManagerConfig, $serviceManagerSetup);
        $originalServiceManager = new OriginalServiceManager($serviceManager, $serviceManagerConfig, $serviceManagerSetup);

        $result = $originalServiceManager->get(LazyLoadingObject::class);

        $this->assertDirectoryExists($persistRoot . '/lazyLoading');

        $fileCount = 0;
        $files = \scandir($persistRoot . '/lazyLoading');
        foreach ($files as $file) {
            if ($file[0] == '.') {
                continue;
            }
            $fileCount++;
        }
        $this->assertEquals(1, $fileCount);

        $this->recursiveRmDir($persistRoot);

        $this->assertInstanceOf(LazyLoadingObject::class, $result);
        $this->assertTrue(\in_array(TestInterface::class, \class_implements($result)));
        $this->expectException(\Exception::class);
        $this->expectExceptionCode(500);
        $result->doSomething();
    }

    public function testExistingServices()
    {
        $serviceManager = $this->createMock(ServiceManagerInterface::class);

        $services = [
            'service' => new \DateTime(),
        ];
        $factories = [
            'dateTime' => DateTimeFactory::class,
        ];

        $serviceManagerConfig = new ServiceManagerConfig($factories);
        $serviceManagerSetup = new ServiceManagerSetup();

        $originalServiceManager = new OriginalServiceManager($serviceManager, $serviceManagerConfig, $serviceManagerSetup, $services);

        $this->assertSame($services['service'], $originalServiceManager->get('service'));
    }

    private function recursiveRmDir($dir)
    {
        $iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($dir, \FilesystemIterator::SKIP_DOTS), \RecursiveIteratorIterator::CHILD_FIRST);
        foreach ($iterator as $filename => $fileInfo) {
            if ($fileInfo->isDir()) {
                \rmdir($filename);
            } else {
                \unlink($filename);
            }
        }
        \rmdir($dir);
    }
}
