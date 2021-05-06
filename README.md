[![ShipEngine](https://shipengine.github.io/img/shipengine-logo-wide.png)](https://shipengine.com)

ShipEngine PHP
==============

[![Build Status](https://github.com/ShipEngine/shipengine-php/workflows/shipengine-php/badge.svg)](https://github.com/ShipEngine/shipengine-php/actions)
[![Coverage Status](https://coveralls.io/repos/github/ShipEngine/shipengine-php/badge.svg?branch=main&t=SkXqIE)](https://coveralls.io/github/ShipEngine/shipengine-php?branch=main)
[![Latest Unstable Version](https://poser.pugx.org/shipengine/shipengine/v/unstable)](//packagist.org/packages/shipengine/shipengine)
[![License](https://poser.pugx.org/shipengine/shipengine/license)](//packagist.org/packages/shipengine/shipengine)

> :warning: **WARNING**: This is alpha software under active development. `Caveat emptor` until a 0.1.0 release is ready.

A PHP library built on the [ShipEngine API](https://shipengine.com) offering low-level access as well as convenience methods.

</hr>

Quick Start
===========
Install ShipEngine via [Composer](https://getcomposer.org/):
```bash
composer require shipengine/shipengine
```
- The only configuration requirement is an [API key](https://www.shipengine.com/docs/auth/#api-keys).

> The following example assumes that you have already set the `SHIPENGIEN_API_KEY` using `putenv()`.

`Examples`
----------
- [Validate an Address](./docs/addressValidateExample.md)
- [Normalize an Address](./docs/normalizeAddressExample.md)

Instantiate ShipEngine Class
------------------------------
```php
<?php declare(strict_types=1);

require __DIR__ . '/vendor/autoload.php';

use ShipEngine\ShipEngine;

$api_key = getenv('SHIPENGINE_API_KEY');

$shipengine = new ShipEngine($api_key);
```
- You can also pass the **ShipEngine** object an **array** containing `configuration` options instead of a **string**.
```php
<?php declare(strict_types=1);

require __DIR__ . '/vendor/autoload.php';

use ShipEngine\ShipEngine;

$config = array(
    'api_key' => 'baz',
    'page_size' => 75,
    'retries' => 3,
    'timeout' => \DateInterval('PT15000S')
);

$shipengine = new ShipEngine($config);
```

Testing
-------
- You can now run all tests using [PHPUnit](https://phpunit.de/):
_phpunit_
```bash
composer phpunit
```
- You can also run `phpcs`:

_phpcs_
```bash
composer phpcs
```

Lint
----
_phpstan_ using our `composer script`:
```bash
composer phpstan
```

Generate Documentation
----------------------
```bash
composer gen:docs
```

Local Development
=================
We are managing `php environment` with [Nix](https://nixos.org/download.html "Nix Website") and [Direnv](https://direnv.net/docs/installation.html "Direnv Install page"), and we recommend downloading them before contributing to this project.
