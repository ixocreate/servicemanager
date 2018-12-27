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
namespace Ixocreate\ServiceManager;

final class OriginalServiceManager extends \Zend\ServiceManager\ServiceManager
{
    /**
     * OriginalServiceManager constructor.
     * @param ServiceManager $serviceManager
     * @param array $config
     */
    public function __construct(ServiceManager $serviceManager, array $config = [])
    {
        parent::__construct($config);
        $this->creationContext = $serviceManager;
    }
}
