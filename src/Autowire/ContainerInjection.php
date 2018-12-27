<?php
/**
 * @link https://github.com/ixocreate
 * @copyright IXOCREATE GmbH
 * @license MIT License
 */

declare(strict_types=1);

namespace Ixocreate\ServiceManager\Autowire;

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
