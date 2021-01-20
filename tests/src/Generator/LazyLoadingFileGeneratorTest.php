<?php
/**
 * @link https://github.com/ixocreate
 * @copyright IXOLIT GmbH
 * @license MIT License
 */

declare(strict_types=1);

namespace Ixocreate\Test\ServiceManager\Generator;

use Ixocreate\Misc\ServiceManager\LazyLoadingObject;
use Ixocreate\Misc\ServiceManager\ServiceManagerConfig;
use Ixocreate\Misc\ServiceManager\SubManager\DateTimeManagerFactory;
use Ixocreate\ServiceManager\Factory\AutowireFactory;
use Ixocreate\ServiceManager\Generator\LazyLoadingFileGenerator;
use Ixocreate\ServiceManager\ServiceManager;
use Ixocreate\ServiceManager\ServiceManagerSetup;
use Ixocreate\ServiceManager\SubManager\AbstractSubManager;
use Ixocreate\Test\ServiceManager\CleanUpTrait;
use Laminas\ServiceManager\Proxy\LazyServiceFactory;
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

    public function setUp(): void
    {
        $factories = [
            LazyLoadingObject::class => AutowireFactory::class,
        ];
        $delegators = [
            LazyLoadingObject::class => [LazyServiceFactory::class],
        ];
        $lazyServices = [
            LazyLoadingObject::class => LazyLoadingObject::class,
        ];
        $subManagers = [
            AbstractSubManager::class => DateTimeManagerFactory::class,
        ];

        $serviceManagerConfig = new ServiceManagerConfig($factories, $delegators, [], $lazyServices, [], $subManagers);

        $this->serviceManager = new ServiceManager(
            $serviceManagerConfig,
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
            'className' => LazyLoadingObject::class,
            'factory' => LazyLoadingValueHolderFactory::class,
            'proxyManagerVersion' => Version::getVersion(),
            'proxyOptions' => [],
        ];

        $proxyConfiguration = new Configuration();
        $filename = $proxyConfiguration->getClassNameInflector()->getProxyClassName(
            LazyLoadingObject::class,
            $proxyParameters
        );
        $filename = $this->serviceManager->serviceManagerSetup()->getLazyLoadingLocation() . \str_replace(
            '\\',
            '',
            $filename
        ) . '.php';

        $this->serviceManager->get(LazyLoadingObject::class);

        $this->assertFileExists($filename);
    }
}
