<?php
/**
 * @link https://github.com/ixocreate
 * @copyright IXOCREATE GmbH
 * @license MIT License
 */

declare(strict_types=1);

namespace Ixocreate\Test\ServiceManager\Autowire\FactoryResolver;

use Ixocreate\ServiceManager\Autowire\FactoryCode;
use Ixocreate\ServiceManager\Autowire\FactoryResolver\FileFactoryResolver;
use Ixocreate\Misc\ServiceManager\ResolverTestObjectNoConstructor;
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
