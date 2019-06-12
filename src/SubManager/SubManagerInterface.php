<?php
/**
 * @link https://github.com/ixocreate
 * @copyright IXOLIT GmbH
 * @license MIT License
 */

declare(strict_types=1);

namespace Ixocreate\ServiceManager\SubManager;

use Ixocreate\ServiceManager\ServiceManagerInterface;

interface SubManagerInterface extends ServiceManagerInterface
{
    /**
     * @return string
     */
    public static function validation(): ?string;
}
