<?php
/**
 * @link https://github.com/ixocreate
 * @copyright IXOLIT GmbH
 * @license MIT License
 */

declare(strict_types=1);

namespace Ixocreate\ServiceManager;

use Ixocreate\ServiceManager\Autowire\FactoryResolver\FactoryResolverInterface;
use Psr\Container\ContainerInterface;

interface ServiceManagerInterface extends ContainerInterface
{
    /**
     * @param string $id
     * @param array|null $options
     * @return mixed
     */
    public function build(string $id, array $options = null);

    /**
     * @return ServiceManagerConfigInterface
     */
    public function serviceManagerConfig(): ServiceManagerConfigInterface;

    /**
     * @return ServiceManagerSetupInterface
     */
    public function serviceManagerSetup(): ServiceManagerSetupInterface;

    /**
     * @return FactoryResolverInterface
     */
    public function factoryResolver(): FactoryResolverInterface;

    /**
     * @return array
     */
    public function services(): array;

    /**
     * @return array
     */
    public function initialServices(): array;
}
