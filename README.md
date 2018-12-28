**This is a draft. Don't use in production**

# IXOCREATE servicemanager

ixocreate/servicemanager is a psr-11 container library

[![Build Status](https://travis-ci.com/ixocreate/servicemanager.svg?branch=master)](https://travis-ci.com/ixocreate/servicemanager)
[![Coverage Status](https://coveralls.io/repos/github/ixocreate/servicemanager/badge.svg?branch=develop)](https://coveralls.io/github/ixocreate/servicemanager?branch=develop)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/a0f2c8b5-b9a6-4a58-b06f-00648fe90041/mini.png)](https://insight.sensiolabs.com/projects/a0f2c8b5-b9a6-4a58-b06f-00648fe90041)
[![Packagist](https://img.shields.io/packagist/v/ixocreate/servicemanager.svg)](https://packagist.org/packages/ixocreate/servicemanager)
[![Packagist Pre Release](https://img.shields.io/packagist/vpre/ixocreate/servicemanager.svg)](https://packagist.org/packages/ixocreate/servicemanager)
[![Packagist](https://img.shields.io/packagist/l/ixocreate/servicemanager.svg)](https://packagist.org/packages/ixocreate/servicemanager)

## Installation

Install the package via composer:

```sh
composer require ixocreate/:package_name
```

## About ixocreate/servicemanager
ixocreate/servicemanager is built on top of [zendframework/zend-servicemanager](https://github.com/zendframework/zend-servicemanager). Like 
`zend-servicemanager` it is a factory based approach.

### Example

```php
$configurator = new ServiceManagerConfigurator();
$configurator->addFactory(SomeObject::class);
$configurator->addFactory(AnotherObject::class, AnotherObjectFactory::class);
$configurator->addLazyService(SomeObject::class);
$serviceManager = new ServiceManager($configurator->getServiceManagerConfig(), new ServiceManagerSetup());

$serviceManager->get(SomeObject::class);
$serviceManager->build(AnotherObject::class);
```

### Factories
A factory is any class that implements the interface `Ixocreate\Contract\ServiceManager\FactoryInterface`.

```php
class SomeObjectFactory implements FactoryInterface
{
    public function __invoke(ServiceManagerInterface $container, $requestedName, array $options = null)
    {
        return new SomeObject();
    }
}

$configurator = new ServiceManagerConfigurator();
$configurator->addFactory(SomeObject::class, SomeObjectFactory::class);
$serviceManager = new ServiceManager($configurator->getServiceManagerConfig(), new ServiceManagerSetup());

$serviceManager->get(SomeObject::class);
```

## License

The MIT License (MIT). Please see [LICENSE](LICENSE) for more information.
