<?php
/**
 * @link https://github.com/ixocreate
 * @copyright IXOLIT GmbH
 * @license MIT License
 */

declare(strict_types=1);

namespace Ixocreate\Test\ServiceManager\Factory;

use Ixocreate\Misc\ServiceManager\DateTimeFactory;
use Ixocreate\Misc\ServiceManager\ResolverTestObject;
use Ixocreate\Misc\ServiceManager\ServiceManagerConfig;
use Ixocreate\Misc\ServiceManager\SubManager\DateTimeManager;
use Ixocreate\Misc\ServiceManager\SubManager\DateTimeManagerFactory;
use Ixocreate\ServiceManager\Factory\AutowireFactory;
use Ixocreate\ServiceManager\ServiceManager;
use Ixocreate\ServiceManager\ServiceManagerSetup;
use PHPUnit\Framework\TestCase;

class AutowireFactoryTest extends TestCase
{
    /**
     * @var ServiceManager
     */
    private $serviceManager;

    public function setUp()
    {
        $factories = [
            \DateTime::class => DateTimeFactory::class,
            'someThing' => DateTimeFactory::class,
            ResolverTestObject::class => AutowireFactory::class,
        ];
        $subManagers = [
            DateTimeManager::class => DateTimeManagerFactory::class,
        ];

        $serviceManagerConfig = new ServiceManagerConfig($factories, [], [], [], [], $subManagers);

        $this->serviceManager = new ServiceManager(
            $serviceManagerConfig,
            new ServiceManagerSetup()
        );
    }

    public function testInvoke()
    {
        $autoWireFactory = new AutowireFactory();
        $result = $autoWireFactory($this->serviceManager, ResolverTestObject::class);

        $this->assertInstanceOf(ResolverTestObject::class, $result);
    }
}
