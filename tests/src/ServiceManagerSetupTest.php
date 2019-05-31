<?php
/**
 * @link https://github.com/ixocreate
 * @copyright IXOLIT GmbH
 * @license MIT License
 */

declare(strict_types=1);

namespace Ixocreate\Test\ServiceManager;

use Ixocreate\ServiceManager\ServiceManagerSetup;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Ixocreate\ServiceManager\ServiceManagerSetup
 */
class ServiceManagerSetupTest extends TestCase
{
    public function testDefaults()
    {
        $serviceManagerSetup = new ServiceManagerSetup();
        $this->assertEquals(
            'resources/generated/servicemanager/lazyLoading/',
            $serviceManagerSetup->getLazyLoadingLocation()
        );
        $this->assertEquals(
            'resources/generated/servicemanager/autowire/',
            $serviceManagerSetup->getAutowireLocation()
        );
        $this->assertFalse($serviceManagerSetup->isPersistLazyLoading());
        $this->assertFalse($serviceManagerSetup->isPersistAutowire());
    }

    public function testValues()
    {
        $setup = [
            'persistRoot' => 'resources/test',
            'persistLazyLoading' => true,
            'persistAutowire' => true,
        ];
        $serviceManagerSetup = new ServiceManagerSetup($setup['persistRoot'], $setup['persistLazyLoading'], $setup['persistAutowire']);
        $this->assertEquals($setup['persistRoot'] . '/lazyLoading/', $serviceManagerSetup->getLazyLoadingLocation());
        $this->assertEquals($setup['persistRoot'] . '/autowire/', $serviceManagerSetup->getAutowireLocation());
        $this->assertEquals($setup['persistLazyLoading'], $serviceManagerSetup->isPersistLazyLoading());
        $this->assertEquals($setup['persistAutowire'], $serviceManagerSetup->isPersistLazyLoading());
    }

    public function testWithPersistRoot()
    {
        $originalSetup = new ServiceManagerSetup('resources/test');
        $updatedSetup = $originalSetup->withPersistRoot('resources/test1');

        $this->assertEquals('resources/test/lazyLoading/', $originalSetup->getLazyLoadingLocation());
        $this->assertEquals('resources/test1/lazyLoading/', $updatedSetup->getLazyLoadingLocation());
        $this->assertEquals('resources/test1/autowire/', $updatedSetup->getAutowireLocation());

        $updatedSetup = $originalSetup->withPersistRoot('resources/test2/');
        $this->assertEquals('resources/test2/lazyLoading/', $updatedSetup->getLazyLoadingLocation());
        $this->assertEquals('resources/test2/autowire/', $updatedSetup->getAutowireLocation());
    }

    public function testWithPersistLazyLoading()
    {
        $setup = [
            'persistRoot' => 'resources/test',
        ];
        $originalSetup = new ServiceManagerSetup($setup['persistRoot'], false);
        $updatedSetup = $originalSetup->withPersistLazyLoading(true);

        $this->assertFalse($originalSetup->isPersistLazyLoading());
        $this->assertTrue($updatedSetup->isPersistLazyLoading());
    }

    public function testWithPersistAutowire()
    {
        $setup = [
            'persistRoot' => 'resources/test',
        ];
        $originalSetup = new ServiceManagerSetup($setup['persistRoot'], null, false);
        $updatedSetup = $originalSetup->withPersistAutowire(true);

        $this->assertFalse($originalSetup->isPersistAutowire());
        $this->assertTrue($updatedSetup->isPersistAutowire());
    }
}
