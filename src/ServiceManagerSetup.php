<?php
/**
 * @link https://github.com/ixocreate
 * @copyright IXOLIT GmbH
 * @license MIT License
 */

declare(strict_types=1);

namespace Ixocreate\ServiceManager;

final class ServiceManagerSetup implements ServiceManagerSetupInterface
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
     * @var string
     */
    private $persistAutowireLocation = 'autowire/';

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
     *
     * @param null|string $persistRoot
     * @param bool|null $persistLazyLoading
     * @param bool|null $persistAutowire
     * @internal param array $config
     */
    public function __construct(
        ?string $persistRoot = null,
        ?bool $persistLazyLoading = null,
        ?bool $persistAutowire = null
    ) {
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
     * @return string
     */
    public function getAutowireLocation(): string
    {
        return $this->persistRoot . $this->persistAutowireLocation;
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
     * @return ServiceManagerSetupInterface
     */
    public function withPersistRoot(string $persistRoot): ServiceManagerSetupInterface
    {
        $setup = clone $this;
        $setup->persistRoot = \rtrim($persistRoot, '/') . '/';

        return $setup;
    }

    /**
     * @param bool $persistLazyLoading
     * @return ServiceManagerSetupInterface
     */
    public function withPersistLazyLoading(bool $persistLazyLoading): ServiceManagerSetupInterface
    {
        $setup = clone $this;
        $setup->persistLazyLoading = $persistLazyLoading;

        return $setup;
    }

    /**
     * @param bool $persistAutowire
     * @return ServiceManagerSetup
     * @internal param bool $persistLazyLoading
     */
    public function withPersistAutowire(bool $persistAutowire): ServiceManagerSetupInterface
    {
        $setup = clone $this;
        $setup->persistAutowire = $persistAutowire;

        return $setup;
    }
}
