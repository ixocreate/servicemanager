# IXOCREATE Servicemanager Library

[![Build Status](https://travis-ci.com/ixocreate/servicemanager.svg?branch=master)](https://travis-ci.com/ixocreate/servicemanager)
[![Coverage Status](https://coveralls.io/repos/github/ixocreate/servicemanager/badge.svg?branch=master)](https://coveralls.io/github/ixocreate/servicemanager?branch=master)
[![Packagist](https://img.shields.io/packagist/v/ixocreate/servicemanager.svg)](https://packagist.org/packages/ixocreate/servicemanager)
[![PHP Version](https://img.shields.io/packagist/php-v/ixocreate/servicemanager.svg)](https://packagist.org/packages/ixocreate/servicemanager)
[![License](https://img.shields.io/github/license/ixocreate/servicemanager.svg)](LICENSE)

IXOCREATE Servicemanager is a PSR 11 container library utilizing [zendframework/zend-servicemanager](https://github.com/zendframework/zend-servicemanager).

## Installation

Install the package via composer:

```sh
composer require ixocreate/servicemanager
```

## Testing

```sh
composer install --dev
phpunit
```

## Usage

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

A factory is any class that implements the interface `Ixocreate\ServiceManager\FactoryInterface`.

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

## Documentation

Learn more about IXOCREATE by reading its [Documentation](https://ixocreate.github.io/).

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Security Vulnerabilities

If you discover security vulnerabilities, please address issues directly to opensource@ixocreate.com via e-mail.

## License

The MIT License (MIT). Please see [LICENSE](LICENSE) for more information.
