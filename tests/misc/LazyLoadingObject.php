<?php
/**
 * @link https://github.com/ixocreate
 * @copyright IXOCREATE GmbH
 * @license MIT License
 */

declare(strict_types=1);

namespace IxocreateMisc\ServiceManager;

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
