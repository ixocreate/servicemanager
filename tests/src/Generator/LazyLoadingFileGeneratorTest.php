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
use KiwiSuiteTest\ServiceManager\CleanUpTrait;
use PHPUnit\Framework\TestCase;
use ProxyManager\Configuration;
use ProxyManager\Factory\LazyLoadingValueHolderFactory;
use ProxyManager\Version;

class LazyLoadingFileGeneratorTest extends TestCase
{
    use CleanUpTrait;
    /**
     * @var ServiceManager
     */
    private $serviceManager;

    public function setUp()
    {
        $serviceManagerConfigurator = new ServiceManagerConfigurator();
        $serviceManagerConfigurator->addFactory(LazyLoadingObject::class);
        $serviceManagerConfigurator->addLazyService(LazyLoadingObject::class);

        $this->serviceManager = new ServiceManager(
            $serviceManagerConfigurator->getServiceManagerConfig(),
            new ServiceManagerSetup()
        );
    }

    /**
     * @runInSeparateProcess
     */
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

        $this->serviceManager->get(LazyLoadingObject::class);

        $this->assertFileExists($filename);
    }
}
