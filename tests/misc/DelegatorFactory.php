<?php
/**
 * @link https://github.com/ixocreate
 * @copyright IXOCREATE GmbH
 * @license MIT License
 */

declare(strict_types=1);

namespace IxocreateMisc\ServiceManager;


use Ixocreate\Contract\ServiceManager\DelegatorFactoryInterface;
use Ixocreate\Contract\ServiceManager\ServiceManagerInterface;

class DelegatorFactory implements DelegatorFactoryInterface
{
    public function __invoke(ServiceManagerInterface $container, $name, callable $callback, array $options = null)
    {
        return new \DateTime();
    }
}
