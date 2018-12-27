<?php
/**
 * kiwi-suite/servicemanager (https://github.com/kiwi-suite/servicemanager)
 *
 * @package kiwi-suite/servicemanager
 * @link https://github.com/kiwi-suite/servicemanager
 * @copyright Copyright (c) 2010 - 2018 kiwi suite GmbH
 * @license MIT License
 */

declare(strict_types=1);
namespace Ixocreate\ServiceManager\Autowire\FactoryResolver;

use Ixocreate\Contract\ServiceManager\Autowire\FactoryResolverInterface;
use Ixocreate\Contract\ServiceManager\FactoryInterface;
use Ixocreate\ServiceManager\Autowire\FactoryCode;

final class FileFactoryResolver implements FactoryResolverInterface
{
    /**
     * @var FactoryCode
     */
    private $factoryCode;

    /**
     * RuntimeFactoryResolver constructor.
     * @param FactoryCode $factoryCode
     */
    public function __construct(FactoryCode $factoryCode)
    {
        $this->factoryCode = $factoryCode;
    }

    /**
     * @param string $requestedName
     * @param array|null $options
     * @return FactoryInterface
     */
    public function getFactory(string $requestedName, array $options = null): FactoryInterface
    {
        $factoryName = $this->factoryCode->generateFactoryFullQualifiedName($requestedName);

        return new $factoryName();
    }
}
