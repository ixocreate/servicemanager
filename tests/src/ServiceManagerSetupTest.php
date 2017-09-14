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
namespace KiwiSuiteTest\ServiceManager;

use KiwiSuite\ServiceManager\Resolver\CacheResolver;
use KiwiSuite\ServiceManager\Resolver\InMemoryResolver;
use KiwiSuite\ServiceManager\Resolver\ReflectionResolver;
use KiwiSuite\ServiceManager\ServiceManagerSetup;
use PHPUnit\Framework\TestCase;

class ServiceManagerSetupTest extends TestCase
{
    public function testDefaults()
    {
        $serviceManagerSetup = new ServiceManagerSetup();
        $this->assertEquals(InMemoryResolver::class, $serviceManagerSetup->getAutowireResolver());
        $this->assertEquals('resources/generated/servicemanger/autowire/', $serviceManagerSetup->getAutowireLocation());
    }

    public function testValues()
    {
        $setup = [
            'autowireResolver' => ReflectionResolver::class,
            'persistRoot' => 'resources/test',
        ];
        $serviceManagerSetup = new ServiceManagerSetup($setup);
        $this->assertEquals($setup['autowireResolver'], $serviceManagerSetup->getAutowireResolver());
        $this->assertEquals($setup['persistRoot'] . '/autowire/', $serviceManagerSetup->getAutowireLocation());
        $this->assertEquals($setup['persistRoot'] . '/lazyLoading/', $serviceManagerSetup->getLazyLoadingLocation());
        $this->assertEquals($setup['persistRoot'] . '/autowire/autowire.cache', $serviceManagerSetup->getAutowireCacheFileLocation());
    }

    public function testWithAutowireResolver()
    {
        $setup = [
            'autowireResolver' => ReflectionResolver::class,
            'persistRoot' => 'resources/test',
        ];
        $serviceManagerSetup = new ServiceManagerSetup($setup);
        $serviceManagerSetup = $serviceManagerSetup->withAutowireResolver(CacheResolver::class);

        $this->assertEquals(CacheResolver::class, $serviceManagerSetup->getAutowireResolver());
        $this->assertEquals($setup['persistRoot'] . '/autowire/', $serviceManagerSetup->getAutowireLocation());
    }

    public function testWithPersistRoot()
    {
        $setup = [
            'autowireResolver' => ReflectionResolver::class,
            'persistRoot' => 'resources/test',
        ];
        $serviceManagerSetup = new ServiceManagerSetup($setup);
        $serviceManagerSetup = $serviceManagerSetup->withPersistRoot('resources/test1');

        $this->assertEquals(ReflectionResolver::class, $serviceManagerSetup->getAutowireResolver());
        $this->assertEquals('resources/test1/autowire/', $serviceManagerSetup->getAutowireLocation());
    }
}
