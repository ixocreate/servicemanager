<?php
/**
 * @link https://github.com/ixocreate
 * @copyright IXOLIT GmbH
 * @license MIT License
 */

declare(strict_types=1);

namespace Ixocreate\ServiceManager;

interface ServiceManagerConfigInterface
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
    public function getInitializers(): array;

    /**
     * @return array
     */
    public function getLazyServices(): array;

    /**
     * @return array
     */
    public function getNamedServices(): array;
}
