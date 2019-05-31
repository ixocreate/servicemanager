<?php
/**
 * @link https://github.com/ixocreate
 * @copyright IXOLIT GmbH
 * @license MIT License
 */

declare(strict_types=1);

namespace Ixocreate\ServiceManager;

/**
 * Interface NamedServiceInterface
 * @package Ixocreate\ServiceManager
 * @deprecated Use Ixocreate\Application\ServiceManager\NamedServiceInterface
 */
interface NamedServiceInterface
{
    public static function serviceName(): string;
}
