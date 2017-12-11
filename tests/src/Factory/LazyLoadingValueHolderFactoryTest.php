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
namespace KiwiSuiteTest\ServiceManager\Factory;

use KiwiSuite\ServiceManager\Factory\LazyLoadingValueHolderFactory;
use KiwiSuite\ServiceManager\ServiceManager;
use KiwiSuite\ServiceManager\ServiceManagerConfig;
use KiwiSuite\ServiceManager\ServiceManagerSetup;
use KiwiSuiteTest\ServiceManager\CleanUpTrait;
use PHPUnit\Framework\TestCase;

class LazyLoadingValueHolderFactoryTest extends TestCase
{
    use CleanUpTrait;

    /**
     * @var ServiceManager
     */
    private $serviceManagerDefault;

    /**
     * @var ServiceManager
     */
    private $serviceManagerPersist;

    public function setUp()
    {
        $this->serviceManagerDefault = new ServiceManager(new ServiceManagerConfig([]), new ServiceManagerSetup());

        $this->serviceManagerPersist = new ServiceManager(new ServiceManagerConfig([]), new ServiceManagerSetup(
            null,
            true
        ));
    }

    public function testWithPersist()
    {
        $lazyLoadingValueHolderFactory = new LazyLoadingValueHolderFactory();

        $result = $lazyLoadingValueHolderFactory($this->serviceManagerPersist, "something");

        $this->assertInstanceOf(\ProxyManager\Factory\LazyLoadingValueHolderFactory::class, $result);
        $this->assertFileExists($this->serviceManagerPersist->getServiceManagerSetup()->getLazyLoadingLocation());
    }

    public function testWithoutPersist()
    {
        $lazyLoadingValueHolderFactory = new LazyLoadingValueHolderFactory();

        $result = $lazyLoadingValueHolderFactory($this->serviceManagerDefault, "something");

        $this->assertInstanceOf(\ProxyManager\Factory\LazyLoadingValueHolderFactory::class, $result);
    }
}
