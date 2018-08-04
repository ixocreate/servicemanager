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
namespace KiwiSuite\ServiceManager\Autowire;

use Zend\Di\Resolver\AbstractInjection;

final class ContainerInjection extends AbstractInjection
{
    /**
     * Holds the type name to look up
     *
     * @var string
     */
    private $type;

    /**
     * @var null|string
     */
    private $container;

    /**
     * Constructor
     *
     * @param string $type
     * @param string|null $container
     */
    public function __construct(string $type, string $container = null)
    {
        $this->type = $type;
        $this->container = $container;
    }

    /**
     *
     * @return string
     */
    public function getType() : string
    {
        return $this->type;
    }

    /**
     * @return null|string
     */
    public function getContainer() : ?string
    {
        return $this->container;
    }

    /**
     * @return string
     */
    public function export() : string
    {
        return "";
    }

    /**
     * @return bool
     */
    public function isExportable() : bool
    {
        return false;
    }

    /**
     *
     * @return string
     */
    public function __toString() : string
    {
        return $this->type;
    }
}
