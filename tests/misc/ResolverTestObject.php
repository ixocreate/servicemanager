<?php
/**
 * @link https://github.com/ixocreate
 * @copyright IXOCREATE GmbH
 * @license MIT License
 */

declare(strict_types=1);

namespace Ixocreate\Misc\ServiceManager;

class ResolverTestObject
{
    /**
     * ResolverTestObject constructor.
     *
     * @param \DateTime $dateTime
     * @param \DateTimeInterface $test1
     */
    public function __construct(\DateTime $dateTime, \DateTimeInterface $test1, \DateTimeInterface $someThing)
    {
    }
}
