<?php
/**
 * @link https://github.com/ixocreate
 * @copyright IXOLIT GmbH
 * @license MIT License
 */

declare(strict_types=1);

namespace Ixocreate\ServiceManager;

interface SubManagerAwareInterface
{
    /**
     * @return array
     */
    public function getSubManagers(): array;
}
