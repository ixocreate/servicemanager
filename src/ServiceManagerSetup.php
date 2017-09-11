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

final class ServiceManagerSetup
{
    /**
     * @var bool
     */
    private $persist = false;

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
        if (\array_key_exists("persist", $config) && \is_bool($config['persist'])) {
            $this->persist = $config['persist'];
        }

        if (\array_key_exists('persistRoot', $config) && \is_string($config['persistRoot'])) {
            $this->persistRoot = \ltrim($config['persistRoot'], '/') . '/';
        }
    }

    /**
     * @return bool
     */
    public function isPersist(): bool
    {
        return $this->persist;
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
    public function getLazyLoadingLocation(): string
    {
        return $this->persistRoot . $this->persistLazyLoadingLocation;
    }

    /**
     * @param bool $persist
     * @return ServiceManagerSetup
     */
    public function withPersist(bool $persist): ServiceManagerSetup
    {
        return new ServiceManagerSetup([
            'persistRoot' => $this->persistRoot,
            'persist' => $persist,
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
            'persist' => $this->persist,
        ]);
    }
}
