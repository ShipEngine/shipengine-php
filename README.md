[![ShipEngine](https://github.com/ShipEngine/shipengine.github.io/raw/master/img/shipengine-logo-wide.png)](https://shipengine.com)

# ShipEngine PHP

[![Build Status](https://github.com/ShipEngine/shipengine-php/workflows/shipengine-php/badge.svg)](https://github.com/ShipEngine/shipengine-php/actions)
[![Coverage Status](https://coveralls.io/repos/github/ShipEngine/shipengine-php/badge.svg?branch=main&t=SkXqIE)](https://coveralls.io/github/ShipEngine/shipengine-php?branch=main)

> ⚠ **WARNING**: This is alpha software under active development. `Caveat emptor` until a 0.1.0 release is ready.

A PHP client built on the [ShipEngine API](https://shipengine.com) offering low-level access as well as convenience methods.

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

$valid = $shipengine->validateAddress(['1 E 161 St'], 'The Bronx', 'NY', '10451', 'US');

assert($valid);
```

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
