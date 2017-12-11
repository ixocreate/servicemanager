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

class FactoryGeneratorTestObject
{
    /**
     * @var \DateTime
     */
    private $dateTime;
    /**
     * @var string
     */
    private $test;
    /**
     * @var \DateTimeInterface
     */
    private $test1;

    /**
     * ResolverTestObject constructor.
     * @param \DateTime $dateTime
     * @param \DateTimeInterface $test1
     */
    public function __construct(\DateTime $dateTime, string $test, \DateTimeInterface $test1)
    {
        $this->dateTime = $dateTime;
        $this->test = $test;
        $this->test1 = $test1;
    }

    /**
     * @return \DateTime
     */
    public function getDateTime(): \DateTime
    {
        return $this->dateTime;
    }

    /**
     * @return string
     */
    public function getTest(): string
    {
        return $this->test;
    }

    /**
     * @return \DateTimeInterface
     */
    public function getTest1(): \DateTimeInterface
    {
        return $this->test1;
    }
}
