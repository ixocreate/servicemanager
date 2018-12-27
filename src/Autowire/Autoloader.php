<?php
/**
 * @link https://github.com/ixocreate
 * @copyright IXOCREATE GmbH
 * @license MIT License
 */

declare(strict_types=1);

namespace Ixocreate\ServiceManager\Autowire;

use Ixocreate\Contract\ServiceManager\ServiceManagerInterface;

final class Autoloader
{
    /**
     * @var ServiceManagerInterface
     */
    private $serviceManager;

    /**
     * Autoloader constructor.
     * @param ServiceManagerInterface $serviceManager
     */
    public function __construct(ServiceManagerInterface $serviceManager)
    {
        $this->serviceManager = $serviceManager;
    }

    /**
     * @param string $className
     * @return bool
     */
    public function __invoke(string $className) : bool
    {
        if (\class_exists($className, false)) {
            return false;
        }

        if (\mb_strpos($className, 'Ixocreate\\GeneratedFactory\\Factory') === false) {
            return false;
        }


        $filename = $this->serviceManager->getServiceManagerSetup()->getAutowireLocation() . \str_replace('Ixocreate\\GeneratedFactory\\', "", $className) . ".php";

        if (!\file_exists($filename)) {
            return false;
        }

        /* @noinspection PhpIncludeInspection */
        return (bool) require_once $filename;
    }
}
