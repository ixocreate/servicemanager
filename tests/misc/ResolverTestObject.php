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
namespace KiwiSuiteMisc\ServiceManager;

class ResolverTestObject
{

    /**
     * ResolverTestObject constructor.
     * @param \DateTime $dateTime
     * @param \DateTimeInterface $test1
     */
    public function __construct(\DateTime $dateTime, \DateTimeInterface $test1, \DateTimeInterface $someThing)
    {
    }
}
