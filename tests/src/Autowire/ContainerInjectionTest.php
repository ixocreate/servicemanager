<?php
/**
 * @link https://github.com/ixocreate
 * @copyright IXOLIT GmbH
 * @license MIT License
 */

declare(strict_types=1);

namespace Ixocreate\Test\ServiceManager\Autowire;

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
