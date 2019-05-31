<?php
/**
 * @link https://github.com/ixocreate
 * @copyright IXOLIT GmbH
 * @license MIT License
 */

declare(strict_types=1);

namespace Ixocreate\Misc\ServiceManager;

use Ixocreate\ServiceManager\InitializerInterface;
use Ixocreate\ServiceManager\ServiceManagerInterface;

class DateTimeInitializer implements InitializerInterface
{
    /**
     * @param ServiceManagerInterface $container
     * @param $instance
     * @return void
     */
    public function __invoke(ServiceManagerInterface $container, $instance): void
    {
        if ($instance instanceof \DateTime) {
            $instance->setDate(2000, 6, 15);
            $instance->setTime(12, 0, 0);
        }
    }
}
