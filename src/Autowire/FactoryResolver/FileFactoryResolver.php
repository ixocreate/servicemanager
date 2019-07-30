<?php
/**
 * @link https://github.com/ixocreate
 * @copyright IXOLIT GmbH
 * @license MIT License
 */

declare(strict_types=1);

namespace Ixocreate\ServiceManager\Autowire\FactoryResolver;

use Ixocreate\ServiceManager\Autowire\FactoryCode;
use Ixocreate\ServiceManager\Autowire\FactoryResolverInterface;
use Ixocreate\ServiceManager\FactoryInterface;

final class FileFactoryResolver implements FactoryResolverInterface
{
    /**
     * @var FactoryCode
     */
    private $factoryCode;

    /**
     * RuntimeFactoryResolver constructor.
     *
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
