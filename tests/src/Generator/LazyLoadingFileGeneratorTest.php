<?php
/**
 * @link https://github.com/ixocreate
 * @copyright IXOCREATE GmbH
 * @license MIT License
 */

declare(strict_types=1);

namespace Ixocreate\Test\ServiceManager\Generator;

use Ixocreate\Misc\ServiceManager\LazyLoadingObject;
use Ixocreate\Misc\ServiceManager\SubManagerFactory;
use Ixocreate\ServiceManager\Factory\AutowireFactory;
use Ixocreate\ServiceManager\Generator\LazyLoadingFileGenerator;
use Ixocreate\ServiceManager\ServiceManager;
use Ixocreate\ServiceManager\ServiceManagerConfigInterface;
use Ixocreate\ServiceManager\ServiceManagerSetup;
use Ixocreate\ServiceManager\SubManager\SubManager;
use Ixocreate\Test\ServiceManager\CleanUpTrait;
use PHPUnit\Framework\TestCase;
use ProxyManager\Configuration;
use ProxyManager\Factory\LazyLoadingValueHolderFactory;
use ProxyManager\Version;
use Zend\ServiceManager\Proxy\LazyServiceFactory;

class LazyLoadingFileGeneratorTest extends TestCase
{
    use CleanUpTrait;

    /**
     * @var ServiceManager
     */
    private $serviceManager;

    public function setUp()
    {
        $factories = [
            LazyLoadingObject::class => AutowireFactory::class,
        ];
        $delegators = [
            LazyLoadingObject::class => [LazyServiceFactory::class],
        ];
        $subManagers = [
            SubManager::class => SubManagerFactory::class,
        ];

        $serviceManagerConfig = $this->createMock(ServiceManagerConfigInterface::class);
        $serviceManagerConfig
            ->method('getFactories')
            ->willReturn($factories);

        $serviceManagerConfig
            ->method('getDelegators')
            ->willReturn($delegators);

        $serviceManagerConfig
            ->method('getSubManagers')
            ->willReturn($subManagers);

        $serviceManagerConfig
            ->method('getLazyServices')
            ->willReturn([
                LazyLoadingObject::class => LazyLoadingObject::class,
            ]);

        $serviceManagerConfig
            ->method('getConfig')
            ->willReturn([
                'factories' => \array_merge($factories, $subManagers),
                'delegators' => $delegators,
                'initializers' => [],
                'shared_by_default' => true,
            ]);

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
        ];

        $proxyConfiguration = new Configuration();
        $filename = $proxyConfiguration->getClassNameInflector()->getProxyClassName(
            LazyLoadingObject::class,
            $proxyParameters
        );
        $filename = $this->serviceManager->getServiceManagerSetup()->getLazyLoadingLocation() . DIRECTORY_SEPARATOR . \str_replace(
            '\\',
            '',
            $filename
        ) . '.php';

        $this->serviceManager->get(LazyLoadingObject::class);

        $this->assertFileExists($filename);
    }
}
