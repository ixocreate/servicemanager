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
     * @var string
     */
    private $persistRoot = 'resources/generated/servicemanager/';

    /**
     * @var string
     */
    private $persistLazyLoadingLocation = 'lazyLoading/';

    /**
     * @var bool
     */
    private $persistLazyLoading = false;

    /**
     * @var bool
     */
    private $persistAutowire = false;

    /**
     * ServiceManagerSetup constructor.
     * @param null|string $persistRoot
     * @param bool|null $persistLazyLoading
     * @param bool|null $persistAutowire
     * @internal param array $config
     */
    public function __construct(?string $persistRoot = null, ?bool $persistLazyLoading = null, ?bool $persistAutowire = null)
    {
        if ($persistRoot !== null) {
            $this->persistRoot = \rtrim($persistRoot, '/') . '/';
        }

        if ($persistLazyLoading !== null) {
            $this->persistLazyLoading = $persistLazyLoading;
        }

        if ($persistAutowire !== null) {
            $this->persistAutowire = $persistAutowire;
        }
    }

    /**
     * @return string
     */
    public function getLazyLoadingLocation(): string
    {
        return $this->persistRoot . $this->persistLazyLoadingLocation;
    }

    /**
     * @return bool
     */
    public function isPersistLazyLoading(): bool
    {
        return $this->persistLazyLoading;
    }

    /**
     * @return bool
     */
    public function isPersistAutowire(): bool
    {
        return $this->persistAutowire;
    }

    /**
     * @param string $persistRoot
     * @return ServiceManagerSetup
     */
    public function withPersistRoot(string $persistRoot): ServiceManagerSetup
    {
        return new ServiceManagerSetup(
            $persistRoot,
            $this->persistLazyLoading,
            $this->persistAutowire
        );
    }

    /**
     * @param bool $persistLazyLoading
     * @return ServiceManagerSetup
     */
    public function withPersistLazyLoading(bool $persistLazyLoading): ServiceManagerSetup
    {
        return new ServiceManagerSetup(
            $this->persistRoot,
            $persistLazyLoading,
            $this->persistAutowire
        );
    }

    /**
     * @param bool $persistAutowire
     * @return ServiceManagerSetup
     * @internal param bool $persistLazyLoading
     */
    public function withPersistAutowire(bool $persistAutowire): ServiceManagerSetup
    {
        return new ServiceManagerSetup(
            $this->persistRoot,
            $this->persistLazyLoading,
            $persistAutowire
        );
    }
}
