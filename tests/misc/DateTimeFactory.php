<?php
/**
 * @link https://github.com/ixocreate
 * @copyright IXOLIT GmbH
 * @license MIT License
 */

declare(strict_types=1);

namespace Ixocreate\Misc\ServiceManager;

use Ixocreate\ServiceManager\FactoryInterface;
use Ixocreate\ServiceManager\ServiceManagerInterface;

class DateTimeFactory implements FactoryInterface
{
    private static $lastContainer;

    /**
     * @param ServiceManagerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return \DateTime|mixed
     */
    public function __invoke(ServiceManagerInterface $container, $requestedName, array $options = null)
    {
        self::$lastContainer = $container;

        return new \DateTime();
    }

    public static function lastContainer(): ?ServiceManagerInterface
    {
        return self::$lastContainer;
    }
}
