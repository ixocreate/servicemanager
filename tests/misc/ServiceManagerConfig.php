<?php
/**
 * @link https://github.com/ixocreate
 * @copyright IXOCREATE GmbH
 * @license MIT License
 */

declare(strict_types=1);

namespace Ixocreate\Misc\ServiceManager;

use Ixocreate\ServiceManager\ServiceManagerConfigInterface;
use Ixocreate\ServiceManager\SubManagerAwareInterface;

final class ServiceManagerConfig implements ServiceManagerConfigInterface, SubManagerAwareInterface
{
    private $factories = [];

    private $delegators = [];

    private $initializers = [];

    private $lazyServices = [];

    private $namesServices = [];

    private $subManager = [];

    /**
     * ServiceManagerConfig constructor.
     * @param array $factories
     * @param array $delegators
     * @param array $initializers
     * @param array $lazyServices
     * @param array $namesServices
     * @param array $subManager
     */
    public function __construct(array $factories, array $delegators = [], array $initializers = [], array $lazyServices = [], array $namesServices = [], array $subManager = [])
    {
        $this->factories = $factories;
        $this->delegators = $delegators;
        $this->initializers = $initializers;
        $this->lazyServices = $lazyServices;
        $this->namesServices = $namesServices;
        $this->subManager = $subManager;
    }

    public function getFactories(): array
    {
        return $this->factories;
    }

    public function getDelegators(): array
    {
        return $this->delegators;
    }

    public function getInitializers(): array
    {
        return $this->initializers;
    }

    public function getLazyServices(): array
    {
        return $this->lazyServices;
    }

    public function getNamedServices(): array
    {
        return $this->namesServices;
    }

    public function getSubManagers(): array
    {
        return $this->subManager;
    }
}
