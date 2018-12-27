<?php
/**
 * @link https://github.com/ixocreate
 * @copyright IXOCREATE GmbH
 * @license MIT License
 */

declare(strict_types=1);

namespace IxocreateTest\ServiceManager\Factory;

use Ixocreate\ServiceManager\Factory\AutowireFactory;
use Ixocreate\ServiceManager\ServiceManager;
use Ixocreate\ServiceManager\ServiceManagerConfig;
use Ixocreate\ServiceManager\ServiceManagerSetup;
use IxocreateMisc\ServiceManager\DateTimeFactory;
use IxocreateMisc\ServiceManager\SubManagerFactory;
use IxocreateMisc\ServiceManager\ResolverTestObject;
use PHPUnit\Framework\TestCase;

class AutowireFactoryTest extends TestCase
{
    /**
     * @var ServiceManager
     */
    private $serviceManager;

    public function setUp()
    {
        $serviceManagerConfig = new ServiceManagerConfig(
            [
                \DateTime::class => DateTimeFactory::class,
                'someThing' => DateTimeFactory::class,
                ResolverTestObject::class => AutowireFactory::class,
            ],
            [
                'subManager1' => SubManagerFactory::class,
            ]
        );

        $this->serviceManager = new ServiceManager($serviceManagerConfig, new ServiceManagerSetup());
    }

    public function testInvoke()
    {
        $autoWireFactory = new AutowireFactory();
        $result = $autoWireFactory($this->serviceManager, ResolverTestObject::class);

        $this->assertInstanceOf(ResolverTestObject::class, $result);
    }
}
