<?php
/**
 * @link https://github.com/ixocreate
 * @copyright IXOLIT GmbH
 * @license MIT License
 */

declare(strict_types=1);

namespace Ixocreate\ServiceManager\Autowire;

use Psr\Container\ContainerInterface;
use Laminas\Di\Resolver\InjectionInterface;

final class ContainerInjection implements InjectionInterface
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
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return null|string
     */
    public function getContainer(): ?string
    {
        return $this->container;
    }

    /**
     * @param ContainerInterface $container
     * @return mixed|string
     */
    public function toValue(ContainerInterface $container)
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function export(): string
    {
        return '';
    }

    /**
     * @return bool
     */
    public function isExportable(): bool
    {
        return false;
    }
}
