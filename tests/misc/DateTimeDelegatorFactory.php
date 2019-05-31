<?php
/**
 * @link https://github.com/ixocreate
 * @copyright IXOLIT GmbH
 * @license MIT License
 */

declare(strict_types=1);

namespace Ixocreate\Misc\ServiceManager;

use Ixocreate\ServiceManager\DelegatorFactoryInterface;
use Ixocreate\ServiceManager\ServiceManagerInterface;

class DateTimeDelegatorFactory implements DelegatorFactoryInterface
{
    public function __invoke(ServiceManagerInterface $container, $name, callable $callback, array $options = null)
    {
        $dateTime = \call_user_func($callback);

        $dateTime->setDate(2000, 6, 15);
        $dateTime->setTime(12, 0, 0);

        return $dateTime;
    }
}
