<?php
namespace KiwiSuiteTest\ServiceManager\Autowire\FactoryResolver;

use KiwiSuite\ServiceManager\Autowire\FactoryCode;
use KiwiSuite\ServiceManager\Autowire\FactoryResolver\FileFactoryResolver;
use KiwiSuiteMisc\ServiceManager\ResolverTestObjectNoConstructor;
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
