<?php
/**
 * @link https://github.com/ixocreate
 * @copyright IXOLIT GmbH
 * @license MIT License
 */

declare(strict_types=1);

namespace Ixocreate\ServiceManager\Autowire\FactoryResolver;

use Ixocreate\ServiceManager\FactoryInterface;

interface FactoryResolverInterface
{
    /**
     * @param string $requestedName
     * @param array $options
     * @return FactoryInterface
     */
    public function getFactory(string $requestedName, array $options = []): FactoryInterface;
}
