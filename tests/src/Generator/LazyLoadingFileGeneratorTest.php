<?php
/**
 * @link https://github.com/ixocreate
 * @copyright IXOCREATE GmbH
 * @license MIT License
 */

declare(strict_types=1);

namespace IxocreateTest\ServiceManager\Generator;

use Ixocreate\ServiceManager\Generator\LazyLoadingFileGenerator;
use Ixocreate\ServiceManager\ServiceManager;
use Ixocreate\ServiceManager\ServiceManagerConfigurator;
use Ixocreate\ServiceManager\ServiceManagerSetup;
use Ixocreate\ServiceManager\SubManager\SubManager;
use IxocreateMisc\ServiceManager\LazyLoadingObject;
use IxocreateMisc\ServiceManager\SubManagerFactory;
use IxocreateTest\ServiceManager\CleanUpTrait;
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
        $serviceManagerConfigurator->addSubManager(SubManager::class, SubManagerFactory::class);

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
