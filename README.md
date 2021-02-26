[![ShipEngine](https://shipengine.github.io/img/shipengine-logo-wide.png)](https://shipengine.com)

# ShipEngine PHP

[![Build Status](https://github.com/ShipEngine/shipengine-php/workflows/shipengine-php/badge.svg)](https://github.com/ShipEngine/shipengine-php/actions)
[![Coverage Status](https://coveralls.io/repos/github/ShipEngine/shipengine-php/badge.svg?branch=main&t=SkXqIE)](https://coveralls.io/github/ShipEngine/shipengine-php?branch=main)
[![Latest Unstable Version](https://poser.pugx.org/shipengine/shipengine/v/unstable)](//packagist.org/packages/shipengine/shipengine)
[![License](https://poser.pugx.org/shipengine/shipengine/license)](//packagist.org/packages/shipengine/shipengine)

> :warning: **WARNING**: This is alpha software under active development. `Caveat emptor` until a 0.1.0 release is ready.

A PHP library built on the [ShipEngine API](https://shipengine.com) offering low-level access as well as convenience methods.

</hr>

## Quick Start

Install ShipEngine via [Composer](https://getcomposer.org/):
```
%> composer require shipengine/shipengine
```

The only configuration requirement is an [API key](https://www.shipengine.com/docs/auth/#api-keys).
```php
use ShipEngine\ShipEngine;

$api_key = getenv('SHIPENGINE_API_KEY');

$shipengine = new ShipEngine(['api_key' => $api_key]);

$new_tag = $shipengine->createTag('shipengine_tag');

echo $new_tag;
```

To increase the flexibility of the ShipEngine library we use [HTTPlug](http://httplug.io).
If you don't already have a [php-http](http://docs.php-http.org/en/latest/) compliant HTTP Client in your project, you'll need to [install one](http://docs.php-http.org/en/latest/httplug/users.html).
ShipEngine will automatically discover it.
But, you can also pass in a configured client manually.

```php
use ShipEngine\ShipEngine;
use Symfony\Component\HttpClient\HttplugClient;

$api_key = getenv('SHIPENGINE_API_KEY');
$http = new HttplugClient();

$shipengine = new ShipEngine(['api_key' => $api_key], $http);
```

> :neckbeard: You can find a more in-depth tutorial of the Addresses Service in our [docs](https://shipengine.github.io/shipengine-php/tutorials/addresses.html).

## Test

You must have [hoverfly](https://hoverfly.io/) running in order to run tests:
```
%> hoverfly -webserver -response-body-files-path simengine > /dev/null &
```

You can now run all tests using [PHPUnit](https://phpunit.de/):
```
%> ./vendor/bin/phpunit
```

To stop hoverfly (after you are done testing):
```
%> hoverctl stop
```

## Lint
```
%> ./vendor/bin/phpstan analyse src --level 5
```
## Generate Documentation
```
%> ./vendor/bin/phpdoc -d src -t doc
```
