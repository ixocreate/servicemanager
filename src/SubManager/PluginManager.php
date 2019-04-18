<?php
/**
 * @link https://github.com/ixocreate
 * @copyright IXOCREATE GmbH
 * @license MIT License
 */

declare(strict_types=1);

namespace Ixocreate\ServiceManager\SubManager;

use Ixocreate\ServiceManager\ServiceManagerInterface;
use Zend\ServiceManager\ServiceManager;

final class PluginManager extends ServiceManager
{
    /**
     * PluginManager constructor.
     *
     * @param ServiceManagerInterface $container
     * @param array $config
     */
    public function __construct(ServiceManagerInterface $container, array $config = [])
    {
        parent::__construct($config);

        $this->creationContext = $container;
    }
}
