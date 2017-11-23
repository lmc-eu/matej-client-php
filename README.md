# PHP API Client for Matej recommendation engine

[![Latest Stable Version](https://img.shields.io/packagist/v/lmc/matej-client.svg?style=flat-square)](https://packagist.org/packages/lmc/matej-client)
[![Travis Build Status](https://img.shields.io/travis/lmc-eu/matej-client-php/master.svg?style=flat-square)](https://travis-ci.org/lmc-eu/matej-client-php)
[![Coverage Status](https://img.shields.io/coveralls/lmc-eu/matej-client-php/master.svg?style=flat-square)](https://coveralls.io/r/lmc-eu/matej-client-php?branch=master)

## Changelog
For latest changes see [CHANGELOG.md](CHANGELOG.md) file. We follow [Semantic Versioning](http://semver.org/).

## Installation

Matej API client library is installed using [Composer](https://getcomposer.org/).

We use [HTTPlug](https://github.com/php-http/httplug) abstraction so that the library is not hard coupled to
any specific HTTP library, allowing you to use HTTP library which fits your needs (or maybe HTTP library you already use).

This means that besides the `lmc/matej-client` library itself you must install a package which provides
`php-http/client-implementation` - see [list of supported clients and adapters](http://docs.php-http.org/en/latest/clients.html).

If you, for example, want to use Guzzle 6 as the underlying HTTP library, install the package like this:

```sh
$ composer require lmc/matej-client php-http/guzzle6-adapter
```

Or if you want to use cURL client:

```sh
$ composer require lmc/matej-client php-http/curl-client guzzlehttp/psr7
```

## Usage

```php
$matej = new Matej('accountId', 'apikey');

// Create new item property in database:
$response = $matej->request()
    ->setupItemProperties()
    ->addProperty(ItemPropertySetup::timestamp('valid_to'))
    ->addProperty(ItemPropertySetup::string('title'))
    ->send();

// Delete item property from database:
$response = $matej->request()
    ->deleteItemProperties()
    ->addProperty(ItemPropertySetup::timestamp('valid_from'))
    ->addProperty(ItemPropertySetup::string('title'))
    ->send();

// Send item property data to database
$response = $matej->request()
    ->events()
    ->addItemProperty(ItemProperty::create('1337', ['valid_from' => time(), 'title' => 'Title']))
    ->send();
```

## Running Tests

For each pull-request, unit tests, as well as static analysis and codestyle checks, must pass.

To run all those checks execute:

```sh
$ composer all
```
