<?php
/**
 * @link https://github.com/ixocreate
 * @copyright IXOLIT GmbH
 * @license MIT License
 */

declare(strict_types=1);

namespace Ixocreate\ServiceManager;

/**
 * Class OriginalServiceManager
 * @package Ixocreate\ServiceManager
 * @internal
 */
final class OriginalServiceManager extends \Laminas\ServiceManager\ServiceManager
{
    /**
     * OriginalServiceManager constructor.
     *
     * required to change creationContext
     *
     * @param ServiceManagerInterface $serviceManager
     * @param ServiceManagerConfigInterface $serviceManagerConfig
     * @param ServiceManagerSetupInterface $serviceManagerSetup
     * @param array $services
     */
    public function __construct(
        ServiceManagerInterface $serviceManager,
        ServiceManagerConfigInterface $serviceManagerConfig,
        ServiceManagerSetupInterface $serviceManagerSetup,
        array $services = []
    ) {
        $factories = $serviceManagerConfig->getFactories();
        if ($serviceManagerConfig instanceof SubManagerAwareInterface) {
            $factories = \array_merge($factories, $serviceManagerConfig->getSubManagers());
        }

        $config = [
            'factories' => $factories,
            'delegators' => $serviceManagerConfig->getDelegators(),
            'initializers' => $serviceManagerConfig->getInitializers(),
            'shared_by_default' => true,
        ];

        $config['services'] = $services;
        $config['lazy_services'] = [
            'class_map' => $serviceManagerConfig->getLazyServices(),
            'proxies_target_dir' => null,
            'proxies_namespace' => null,
            'write_proxy_files' => false,
        ];
        $config['aliases'] = $serviceManagerConfig->getNamedServices();

        if ($serviceManagerSetup->isPersistLazyLoading()) {
            $lazyLoadingLocation = $serviceManagerSetup->getLazyLoadingLocation();
            if (!\file_exists($lazyLoadingLocation)) {
                \mkdir($lazyLoadingLocation, 0777, true);
            }

            $config['lazy_services']['proxies_target_dir'] = $lazyLoadingLocation;
            $config['lazy_services']['write_proxy_files'] = true;
        }

        parent::__construct($config);

        $this->creationContext = $serviceManager;
    }
}
