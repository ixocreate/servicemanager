<?php
/**
 * kiwi-suite/servicemanager (https://github.com/kiwi-suite/servicemanager)
 *
 * @package kiwi-suite/servicemanager
 * @see https://github.com/kiwi-suite/servicemanager
 * @copyright Copyright (c) 2010 - 2018 kiwi suite GmbH
 * @license MIT License
 */

declare(strict_types=1);
namespace KiwiSuiteTest\ServiceManager\Autowire;

use KiwiSuite\ServiceManager\Autowire\ContainerInjection;
use KiwiSuite\ServiceManager\Autowire\DefaultValueInjection;
use KiwiSuite\ServiceManager\Autowire\FactoryCode;
use KiwiSuite\ServiceManager\Factory\AutowireFactory;
use KiwiSuite\ServiceManager\ServiceManager;
use KiwiSuite\ServiceManager\ServiceManagerConfig;
use KiwiSuite\ServiceManager\ServiceManagerSetup;
use KiwiSuiteMisc\ServiceManager\DateTimeFactory;
use KiwiSuiteMisc\ServiceManager\FactoryGeneratorTestObject;
use KiwiSuiteMisc\ServiceManager\ResolverTestObject;
use KiwiSuiteMisc\ServiceManager\SubManagerFactory;
use PHPUnit\Framework\TestCase;
use Zend\Di\Resolver\AbstractInjection;
use Zend\Di\Resolver\ValueInjection;

class FactoryCodeTest extends TestCase
{
    /**
     * @var FactoryCode
     */
    private $factoryCode;

    /**
     * @var ServiceManager
     */
    private $serviceManager;

    public function setUp()
    {
        $this->factoryCode = new FactoryCode();

        $serviceManagerConfig = new ServiceManagerConfig([
            'factories' => [
                \DateTime::class => DateTimeFactory::class,
                'someThing' => DateTimeFactory::class,
                ResolverTestObject::class => AutowireFactory::class,
            ],
            'subManagers' => [
                'subManager1' => SubManagerFactory::class,
            ],
        ]);

        $this->serviceManager = new ServiceManager($serviceManagerConfig, new ServiceManagerSetup());
    }

    public function testGenerateFactoryName()
    {
        $requestedName = \DateTime::class;

        $this->assertSame('Factory' . \md5($requestedName), $this->factoryCode->generateFactoryName($requestedName));
    }

    public function testGenerateFactoryFullQualifiedName()
    {
        $requestedName = \DateTime::class;

        $this->assertSame(
            '\\KiwiSuite\\GeneratedFactory\\Factory' . \md5($requestedName),
            $this->factoryCode->generateFactoryFullQualifiedName($requestedName)
        );
    }

    public function testGenerateCode()
    {
        $resolution = [
            'dateTime'  => new ContainerInjection(\DateTime::class, null),
            'test'      => new ValueInjection("test"),
            'test1'     => new ContainerInjection('test1', 'subManager1'),
            'default1'  => new DefaultValueInjection("default"),
            'default2'  => new DefaultValueInjection(null),
        ];
        /** @var AbstractInjection $injection */
        foreach ($resolution as $key => $injection) {
            $injection->setParameterName($key);
        }

        $requestedName = FactoryGeneratorTestObject::class;
        $factoryName = $this->factoryCode->generateFactoryFullQualifiedName($requestedName);

        $code = $this->factoryCode->generateFactoryCode($requestedName, $resolution);

        $fileName = \tempnam(\sys_get_temp_dir(), $this->factoryCode->generateFactoryName($requestedName) . '.php.tmp.');

        \file_put_contents(
            $fileName,
            $code
        );

        /* @noinspection PhpIncludeInspection */
        require $fileName;
        \unlink($fileName);

        $factory = new $factoryName();

        $object = $factory->__invoke($this->serviceManager, $requestedName, ['test' => 'somestring']);

        $this->assertInstanceOf($requestedName, $object);
        $this->assertSame("somestring", $object->getTest());
        $this->assertSame("default", $object->getDefault1());
        $this->assertNull($object->getDefault2());
        $this->assertInstanceOf(\DateTime::class, $object->getDateTime());
        $this->assertInstanceOf(\DateTime::class, $object->getTest1());
    }
}