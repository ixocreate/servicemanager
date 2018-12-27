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
namespace IxocreateMisc\ServiceManager;

class ComplexObject
{
    public function __construct(
        string $value1,
        ResolverTestObject $resolverTestObject,
        ResolverTestObjectScalar $value2,
        OwnDateTime $dateTime,
        \DateTimeInterface $value3,
        string $value4,
        DefaultParamObject $defaultParamObject
    ) {
    }
}
