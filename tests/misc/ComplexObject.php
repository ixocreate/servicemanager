<?php
/**
 * @link https://github.com/ixocreate
 * @copyright IXOCREATE GmbH
 * @license MIT License
 */

declare(strict_types=1);

namespace Ixocreate\Misc\ServiceManager;

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
