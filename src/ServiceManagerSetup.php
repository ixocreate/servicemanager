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
namespace KiwiSuite\ServiceManager;

use KiwiSuite\ServiceManager\Resolver\InMemoryResolver;

final class ServiceManagerSetup
{
    /**
     * @var string
     */
    private $autowireResolver = InMemoryResolver::class;

    /**
     * @var string
     */
    private $persistRoot = 'resources/generated/servicemanger/';

    /**
     * @var string
     */
    private $persistAutowireLocation = 'autowire/';

    /**
     * @var string
     */
    private $persistLazyLoadingLocation = 'lazyLoading/';

    /**
     * ServiceManagerSetup constructor.
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        if (\array_key_exists("autowireResolver", $config) && \is_string($config['autowireResolver'])) {
            $this->autowireResolver = $config['autowireResolver'];
        }

        if (\array_key_exists('persistRoot', $config) && \is_string($config['persistRoot'])) {
            $this->persistRoot = \ltrim($config['persistRoot'], '/') . '/';
        }
    }

    /**
     * @return string
     */
    public function getAutowireLocation(): string
    {
        return $this->persistRoot . $this->persistAutowireLocation;
    }

    /**
     * @return string
     */
    public function getAutowireCacheFileLocation(): string
    {
        return $this->getAutowireLocation() . 'autowire.cache';
    }

    /**
     * @return string
     */
    public function getLazyLoadingLocation(): string
    {
        return $this->persistRoot . $this->persistLazyLoadingLocation;
    }

    /**
     * @return string
     */
    public function getAutowireResolver(): string
    {
        return $this->autowireResolver;
    }

    /**
     * @param string $autowireResolver
     * @return ServiceManagerSetup
     */
    public function withAutowireResolver(string $autowireResolver): ServiceManagerSetup
    {
        return new ServiceManagerSetup([
            'persistRoot' => $this->persistRoot,
            'autowireResolver' => $autowireResolver,
        ]);
    }

    /**
     * @param string $persistRoot
     * @return ServiceManagerSetup
     */
    public function withPersistRoot(string $persistRoot): ServiceManagerSetup
    {
        return new ServiceManagerSetup([
            'persistRoot' => $persistRoot,
            'autowireResolver' => $this->autowireResolver,
        ]);
    }
}
