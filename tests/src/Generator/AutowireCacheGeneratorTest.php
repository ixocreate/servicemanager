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
namespace KiwiSuiteTest\ServiceManager\Generator;

use KiwiSuite\ServiceManager\Factory\AutowireFactory;
use KiwiSuite\ServiceManager\Generator\AutowireCacheGenerator;
use KiwiSuite\ServiceManager\Resolver\Resolution;
use KiwiSuite\ServiceManager\ServiceManager;
use KiwiSuite\ServiceManager\ServiceManagerConfig;
use KiwiSuite\ServiceManager\ServiceManagerSetup;
use KiwiSuiteMisc\ServiceManager\DateTimeFactory;
use KiwiSuiteMisc\ServiceManager\ResolverTestObject;
use KiwiSuiteMisc\ServiceManager\SubManagerFactory;
use PHPUnit\Framework\TestCase;

class AutowireCacheGeneratorTest extends TestCase
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

        $this->serviceManager = new ServiceManager($serviceManagerConfig, new ServiceManagerSetup());
    }

    public static function tearDownAfterClass()
    {
        if (!\file_exists("resources")) {
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

        \rmdir("resources");
    }

    public function testGenerate()
    {
        $autowireGenerator = new AutowireCacheGenerator();
        $resolutions = $autowireGenerator->generate($this->serviceManager);

        $this->assertArrayHasKey(ResolverTestObject::class, $resolutions);

        /** @var Resolution $resolution */
        $resolution = $resolutions[ResolverTestObject::class];
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

    public function testWrite()
    {
        $autowireGenerator = new AutowireCacheGenerator();
        $resolutions = $autowireGenerator->generate($this->serviceManager);

        $autowireGenerator->write($this->serviceManager, $resolutions);

        $this->assertFileExists($this->serviceManager->getServiceManagerSetup()->getAutowireCacheFileLocation());
        $this->assertStringEqualsFile($this->serviceManager->getServiceManagerSetup()->getAutowireCacheFileLocation(), \serialize($resolutions));
    }
}
