**This is a draft. Don't use in production**

# kiwi-suite/servicemanager

kiwi-suite/servicemanager is a psr-11 container library

[![Build Status](https://travis-ci.org/kiwi-suite/servicemanager.svg?branch=master)](https://travis-ci.org/kiwi-suite/servicemanager)
[![Coverage Status](https://coveralls.io/repos/github/kiwi-suite/servicemanager/badge.svg?branch=develop)](https://coveralls.io/github/kiwi-suite/servicemanager?branch=develop)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/a0f2c8b5-b9a6-4a58-b06f-00648fe90041/mini.png)](https://insight.sensiolabs.com/projects/a0f2c8b5-b9a6-4a58-b06f-00648fe90041)
[![Packagist](https://img.shields.io/packagist/v/kiwi-suite/servicemanager.svg)](https://packagist.org/packages/kiwi-suite/servicemanager)
[![Packagist Pre Release](https://img.shields.io/packagist/vpre/kiwi-suite/servicemanager.svg)](https://packagist.org/packages/kiwi-suite/servicemanager)
[![Packagist](https://img.shields.io/packagist/l/kiwi-suite/servicemanager.svg)](https://packagist.org/packages/kiwi-suite/servicemanager)

## Installation

The suggested installation method is via [composer](https://getcomposer.org/):

```sh
php composer.phar require kiwi-suite/servicemanager
```

## About kiwi-suite/servicemanager
kiwi-suite/servicemanager is built on top of [zendframework/zend-servicemanager](https://github.com/zendframework/zend-servicemanager). Like 
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
A factory is any class that implements the interface `KiwiSuite\ServiceManager\FactoryInterface`.

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

kiwi-suite/servicemanager is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT) - see the `LICENSE` file for details
