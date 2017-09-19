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

use KiwiSuite\ServiceManager\Generator\LazyLoadingFileGenerator;
use KiwiSuite\ServiceManager\ServiceManager;
use KiwiSuite\ServiceManager\ServiceManagerConfigurator;
use KiwiSuite\ServiceManager\ServiceManagerSetup;
use KiwiSuiteMisc\ServiceManager\LazyLoadingObject;
use PHPUnit\Framework\TestCase;
use ProxyManager\Configuration;
use ProxyManager\Factory\LazyLoadingValueHolderFactory;
use ProxyManager\Version;

class LazyLoadingFileGeneratorTest extends TestCase
{
    /**
     * @var ServiceManager
     */
    private $serviceManager;

    public function setUp()
    {
        $serviceManagerConfigurator = new ServiceManagerConfigurator();
        $serviceManagerConfigurator->addFactory(LazyLoadingObject::class);
        $serviceManagerConfigurator->addLazyService(LazyLoadingObject::class);

        $this->serviceManager = new ServiceManager($serviceManagerConfigurator->getServiceManagerConfig(), new ServiceManagerSetup());
    }

    public function tearDown()
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
        $lazyLoadingFileGenerator = new LazyLoadingFileGenerator();
        $lazyLoadingFileGenerator->generate($this->serviceManager);

        $proxyParameters = [
            'className'           => LazyLoadingObject::class,
            'factory'             => LazyLoadingValueHolderFactory::class,
            'proxyManagerVersion' => Version::getVersion(),
        ];

        $proxyConfiguration = new Configuration();
        $filename = $proxyConfiguration->getClassNameInflector()->getProxyClassName(LazyLoadingObject::class, $proxyParameters);
        $filename = $this->serviceManager->getServiceManagerSetup()->getLazyLoadingLocation() . DIRECTORY_SEPARATOR . \str_replace('\\', '', $filename) . '.php';

        $this->assertFileExists($filename);
    }
}
