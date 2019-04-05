<?php
/**
 * @link https://github.com/ixocreate
 * @copyright IXOCREATE GmbH
 * @license MIT License
 */

declare(strict_types=1);

namespace IxocreateMisc\ServiceManager;

use Ixocreate\Contract\ServiceManager\InitializerInterface;
use Ixocreate\Contract\ServiceManager\ServiceManagerInterface;

class Initializer2 implements InitializerInterface
{
    /**
     * @param ServiceManagerInterface $container
     * @param $instance
     * @return void
     */
    public function __invoke(ServiceManagerInterface $container, $instance): void
    {
    }
}
