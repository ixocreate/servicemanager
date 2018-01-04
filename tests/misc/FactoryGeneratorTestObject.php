<?php
/**
 * kiwi-suite/servicemanager (https://github.com/kiwi-suite/servicemanager)
 *
 * @package kiwi-suite/servicemanager
 * @see https://github.com/kiwi-suite/servicemanager
 * @copyright Copyright (c) 2010 - 2018 kiwi suite GmbH
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
     * @var string
     */
    private $default1;
    /**
     * @var null
     */
    private $default2;

    /**
     * ResolverTestObject constructor.
     * @param \DateTime $dateTime
     * @param string $test
     * @param \DateTimeInterface $test1
     * @param string $default1
     * @param null $default2
     */
    public function __construct(\DateTime $dateTime, string $test, \DateTimeInterface $test1, $default1 = "default", $default2 = null)
    {
        $this->dateTime = $dateTime;
        $this->test = $test;
        $this->test1 = $test1;
        $this->default1 = $default1;
        $this->default2 = $default2;
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

    /**
     * @return string
     */
    public function getDefault1(): string
    {
        return $this->default1;
    }

    /**
     * @return null
     */
    public function getDefault2()
    {
        return $this->default2;
    }
}
