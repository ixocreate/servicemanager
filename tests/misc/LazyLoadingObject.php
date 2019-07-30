<?php
/**
 * @link https://github.com/ixocreate
 * @copyright IXOLIT GmbH
 * @license MIT License
 */

declare(strict_types=1);

namespace Ixocreate\Misc\ServiceManager;

class LazyLoadingObject implements TestInterface
{
    public function __construct()
    {
        throw new \Exception("conctructor called", 500);
    }

    public function doSomething()
    {
    }
}
