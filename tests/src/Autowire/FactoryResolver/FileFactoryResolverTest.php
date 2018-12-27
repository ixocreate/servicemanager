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
namespace IxocreateTest\ServiceManager\Autowire\FactoryResolver;

use Ixocreate\ServiceManager\Autowire\FactoryCode;
use Ixocreate\ServiceManager\Autowire\FactoryResolver\FileFactoryResolver;
use IxocreateMisc\ServiceManager\ResolverTestObjectNoConstructor;
use PHPUnit\Framework\TestCase;

class FileFactoryResolverTest extends TestCase
{
    public function testGetFactory()
    {
        $requestedName = ResolverTestObjectNoConstructor::class;
        $factoryCode = new FactoryCode();

        $fileName = \tempnam(\sys_get_temp_dir(), $factoryCode->generateFactoryName($requestedName) . '.php.tmp.');

        \file_put_contents(
            $fileName,
            $factoryCode->generateFactoryCode($requestedName, [])
        );

        /* @noinspection PhpIncludeInspection */
        require $fileName;
        \unlink($fileName);

        $fileFactoryResolver = new FileFactoryResolver($factoryCode);
        $factory = $fileFactoryResolver->getFactory($requestedName);

        $this->assertInstanceOf($factoryCode->generateFactoryFullQualifiedName($requestedName), $factory);
    }
}
