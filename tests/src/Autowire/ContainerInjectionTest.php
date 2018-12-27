<?php
/**
 * kiwi-suite/servicemanager (https://github.com/kiwi-suite/servicemanager)
 *
 * @package kiwi-suite/servicemanager
 * @link https://github.com/kiwi-suite/servicemanager
 * @copyright Copyright (c) 2010 - 2018 kiwi suite GmbH
 * @license MIT License
 */

declare(strict_types=1);
namespace IxocreateTest\ServiceManager\Autowire;

use Ixocreate\ServiceManager\Autowire\ContainerInjection;
use PHPUnit\Framework\TestCase;

class ContainerInjectionTest extends TestCase
{
    public function testContainerInjection()
    {
        $injection = new ContainerInjection("type", "container");
        $this->assertSame("type", $injection->getType());
        $this->assertSame("type", $injection->__toString());
        $this->assertSame("container", $injection->getContainer());
        $this->assertFalse($injection->isExportable());
        $this->assertSame("", $injection->export());
    }
}
