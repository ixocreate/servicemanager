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
namespace KiwiSuiteTest\ServiceManager\Resolver;

use KiwiSuite\ServiceManager\Exception\ServiceNotFoundException;
use KiwiSuite\ServiceManager\Factory\AutowireFactory;
use KiwiSuite\ServiceManager\Generator\AutowireCacheGenerator;
use KiwiSuite\ServiceManager\Resolver\CacheResolver;
use KiwiSuite\ServiceManager\Resolver\InMemoryResolver;
use KiwiSuite\ServiceManager\ServiceManager;
use KiwiSuite\ServiceManager\ServiceManagerConfig;
use KiwiSuite\ServiceManager\ServiceManagerSetup;
use KiwiSuiteMisc\ServiceManager\DateTimeFactory;
use KiwiSuiteMisc\ServiceManager\ResolverTestObject;
use KiwiSuiteMisc\ServiceManager\SubManagerFactory;
use PHPUnit\Framework\TestCase;

class CacheResolverTest extends TestCase
{
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

        $this->serviceManager = new ServiceManager($serviceManagerConfig, new ServiceManagerSetup([
            'autowireResolver' => CacheResolver::class,
        ]));

        $autowireCacheGenerator = new AutowireCacheGenerator();
        $autowireCacheGenerator->write($this->serviceManager, $autowireCacheGenerator->generate($this->serviceManager));
    }

    public function tearDown()
    {
        if (!file_exists("resources")) {
            return;
        }

        $files = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator("resources", \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::CHILD_FIRST
        );

        foreach ($files as $fileinfo) {
            $todo = ($fileinfo->isDir() ? 'rmdir' : 'unlink');
            $todo($fileinfo->getRealPath());
        }

        rmdir("resources");
    }

    public function testResolve()
    {
        $resolver = new CacheResolver();
        $resolution = $resolver->resolveService($this->serviceManager, ResolverTestObject::class);

        $this->assertEquals(ResolverTestObject::class, $resolution->getServiceName());
        $this->assertEquals([
            [
                'serviceName' => \DateTime::class,
                'subManager' => null,
            ],
            [
                'serviceName' => "test1",
                'subManager' => "subManager1",
            ],
            [
                'serviceName' => "someThing",
                'subManager' => null,
            ],
        ], $resolution->getDependencies());
    }

    public function testServiceNotFoundException()
    {
        $this->expectException(ServiceNotFoundException::class);
        $resolver = new CacheResolver();
        $resolver->resolveService($this->serviceManager, "test");
    }

    public function testFileDoesntExists()
    {
        $this->tearDown();
        $this->expectException(ServiceNotFoundException::class);
        $resolver = new CacheResolver();
        $resolver->resolveService($this->serviceManager, ResolverTestObject::class);
    }

    public function testInvalidSerialization()
    {
        file_put_contents($this->serviceManager->getServiceManagerSetup()->getAutowireCacheFileLocation(), "invalid");


        $this->expectException(ServiceNotFoundException::class);
        $resolver = new CacheResolver();
        $resolver->resolveService($this->serviceManager, ResolverTestObject::class);
    }
}
