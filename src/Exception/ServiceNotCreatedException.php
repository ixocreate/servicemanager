<?php
/**
 * @link https://github.com/ixocreate
 * @copyright IXOLIT GmbH
 * @license MIT License
 */

declare(strict_types=1);

namespace Ixocreate\ServiceManager\Exception;

use Psr\Container\ContainerExceptionInterface;

class ServiceNotCreatedException extends \InvalidArgumentException implements ContainerExceptionInterface
{
}
