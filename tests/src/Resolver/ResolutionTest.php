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
namespace KiwiSuiteTest\ServiceManager\Resolver;

use KiwiSuite\ServiceManager\Exception\InvalidArgumentException;
use KiwiSuite\ServiceManager\Resolver\Resolution;
use PHPUnit\Framework\TestCase;

class ResolutionTest extends TestCase
{
    public function testResolution()
    {
        $dependencies= [
            [
                'serviceName' => 'test1',
                'subManager' => null,
            ],
            [
                'serviceName' => 'test1',
                'subManager' => 'subManager1',
            ],
        ];
        $resolution = new Resolution(
            "testResolution",
            $dependencies
        );

        $this->assertEquals("testResolution", $resolution->getServiceName());
        $this->assertEquals($dependencies, $resolution->getDependencies());
    }

    public function testDependencyIsNotAnArray()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionCode(100);
        new Resolution(
            "testResolution",
            ['string']
        );
    }

    public function testDependencyServiceNameNotSet()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionCode(200);
        new Resolution(
            "testResolution",
            [[]]
        );
    }

    public function testDependencyServiceNameNotAString()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionCode(200);
        new Resolution(
            "testResolution",
            [['serviceName' => []]]
        );
    }

    public function testDependencySubManagerNotSet()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionCode(300);
        new Resolution(
            "testResolution",
            [['serviceName' => 'test']]
        );
    }

    public function testDependencySubManagerNotStringOrNull()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionCode(300);
        new Resolution(
            "testResolution",
            [['serviceName' => 'test', 'subManager' => []]]
        );
    }
}
