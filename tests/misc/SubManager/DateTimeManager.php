<?php
/**
 * @link https://github.com/ixocreate
 * @copyright IXOLIT GmbH
 * @license MIT License
 */

declare(strict_types=1);

namespace Ixocreate\Misc\ServiceManager\SubManager;

use Ixocreate\ServiceManager\SubManager\AbstractSubManager;

class DateTimeManager extends AbstractSubManager
{
    public static function getValidation(): ?string
    {
        return \DateTimeInterface::class;
    }
}
