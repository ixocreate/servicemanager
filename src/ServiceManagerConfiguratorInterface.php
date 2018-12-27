<?php
/**
 * @link https://github.com/ixocreate
 * @copyright IXOCREATE GmbH
 * @license MIT License
 */

declare(strict_types=1);

namespace Ixocreate\ServiceManager;

interface ServiceManagerConfiguratorInterface
{
    /**
     * @return array
     */
    public function getFactories(): array;

    /**
     * @return array
     */
    public function getDelegators(): array;

    /**
     * @return array
     */
    public function getLazyServices(): array;

    /**
     * @return array
     */
    public function getInitializers(): array;

    /**
     * @return array
     */
    public function getMetadata(): array;

    /**
     * @return array
     */
    public function getSubManagers(): array;
}
