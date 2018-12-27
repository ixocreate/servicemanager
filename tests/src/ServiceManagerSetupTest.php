<?php
/**
 * @link https://github.com/ixocreate
 * @copyright IXOCREATE GmbH
 * @license MIT License
 */

declare(strict_types=1);

namespace IxocreateTest\ServiceManager;

use Ixocreate\ServiceManager\ServiceManagerSetup;
use PHPUnit\Framework\TestCase;

class ServiceManagerSetupTest extends TestCase
{
    public function testDefaults()
    {
        $serviceManagerSetup = new ServiceManagerSetup();
        $this->assertEquals('resources/generated/servicemanager/autowire/', $serviceManagerSetup->getAutowireLocation());
        $this->assertFalse($serviceManagerSetup->isPersistLazyLoading());
    }

    public function testValues()
    {
        $setup = [
            'persistRoot' => 'resources/test',
            'persistLazyLoading' => true,
        ];
        $serviceManagerSetup = new ServiceManagerSetup($setup['persistRoot'], $setup['persistLazyLoading']);
        $this->assertEquals($setup['persistRoot'] . '/autowire/', $serviceManagerSetup->getAutowireLocation());
        $this->assertEquals($setup['persistRoot'] . '/lazyLoading/', $serviceManagerSetup->getLazyLoadingLocation());
        $this->assertEquals($setup['persistLazyLoading'], $serviceManagerSetup->isPersistLazyLoading());
    }

    public function testWithAutowireResolver()
    {
        $setup = [
            'persistRoot' => 'resources/test',
            'persistLazyLoading' => true,
        ];
        $serviceManagerSetup = new ServiceManagerSetup($setup['persistRoot'], $setup['persistLazyLoading']);

        $this->assertEquals($setup['persistRoot'] . '/autowire/', $serviceManagerSetup->getAutowireLocation());
        $this->assertTrue($serviceManagerSetup->isPersistLazyLoading());
    }

    public function testWithPersistRoot()
    {
        $setup = [
            'persistRoot' => 'resources/test',
            'persistLazyLoading' => true,
        ];
        $serviceManagerSetup = new ServiceManagerSetup($setup['persistRoot'], $setup['persistLazyLoading']);
        $serviceManagerSetup = $serviceManagerSetup->withPersistRoot('resources/test1');

        $this->assertEquals('resources/test1/autowire/', $serviceManagerSetup->getAutowireLocation());
        $this->assertTrue($serviceManagerSetup->isPersistLazyLoading());
    }

    public function testWithPersistLazyLoading()
    {
        $setup = [
            'persistRoot' => 'resources/test',
        ];
        $serviceManagerSetup = new ServiceManagerSetup($setup['persistRoot']);
        $serviceManagerSetup = $serviceManagerSetup->withPersistLazyLoading(true);

        $this->assertEquals($setup['persistRoot'] . '/autowire/', $serviceManagerSetup->getAutowireLocation());
        $this->assertTrue($serviceManagerSetup->isPersistLazyLoading());
    }

    public function testWithPersistAutowire()
    {
        $setup = [
            'persistRoot' => 'resources/test',
        ];
        $serviceManagerSetup = new ServiceManagerSetup($setup['persistRoot']);
        $serviceManagerSetup = $serviceManagerSetup->withPersistAutowire(true);

        $this->assertEquals($setup['persistRoot'] . '/autowire/', $serviceManagerSetup->getAutowireLocation());
        $this->assertTrue($serviceManagerSetup->isPersistAutowire());
    }
}
