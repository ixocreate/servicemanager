<?php
namespace KiwiSuiteMisc\ServiceManager;

class ComplexObject
{
    public function __construct(
        string $value1,
        ResolverTestObject $resolverTestObject,
        ResolverTestObjectScalar $value2,
        OwnDateTime $dateTime,
        \DateTimeInterface $value3,
        string $value4
    ) {

    }
}
