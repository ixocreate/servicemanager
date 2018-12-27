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

use Ixocreate\Contract\ServiceManager\FactoryInterface;

class ResolverTestObjectNoDep
{

    /**
     * ResolverTestObject constructor.
     * @param \DateTime $dateTime
     * @param \DateTimeInterface $test1
     */
    public function __construct(\DateTime $dateTime, FactoryInterface $test_doesnt_exist)
    {
    }
}
