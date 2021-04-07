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
    'timeout' => 15000,
    'client' => null  // Specify null to use the default ShipEngine client.
);

$shipengine = new ShipEngine($config);
```

`Examples`
----------
- [Address Validation](./docs/addressValidateExamples.md)

`Track a Package`
-----------------
```php
<?php declare(strict_types=1);

require __DIR__ . '/vendor/autoload.php';

use ShipEngine\ShipEngine;

$api_key = 'SHIPENGINE_API_KEY';

$shipengine = new ShipEngine($api_key);

$tracking_data = $shipengine->trackPackage('ups', 'abc123');

print_r($tracking_data);
```

`Create a Tag`
------------
```php
<?php declare(strict_types=1);

require __DIR__ . '/vendor/autoload.php';

use ShipEngine\ShipEngine;

$api_key = getenv('SHIPENGINE_API_KEY');

$shipengine = new ShipEngine($api_key);

$new_tag = $shipengine->createTag('shipengine_sdk');

print_r($new_tag);
```

- To increase the flexibility of the ShipEngine library we use [HTTPlug](http://httplug.io).
If you don't already have a [php-http](http://docs.php-http.org/en/latest/) compliant HTTP Client in your project, you'll need to [install one](http://docs.php-http.org/en/latest/httplug/users.html).
ShipEngine will automatically discover it.
But, you can also pass in a configured client manually.

Pass the ShipEngine Class a custom client
-----------------------------------------
```php
<?php declare(strict_types=1);

require __DIR__ . '/vendor/autoload.php';

use ShipEngine\ShipEngine;
use Symfony\Component\HttpClient\HttplugClient;

$api_key = getenv('SHIPENGINE_API_KEY');
$http = new HttplugClient();

$shipengine = new ShipEngine($api_key, $http);
```

Test
----

- You must have [hoverfly](https://hoverfly.io/) running in order to run tests:
```bash
hoverfly -webserver -response-body-files-path simengine > /dev/null &
```

- You can now run all tests using [PHPUnit](https://phpunit.de/):
```bash
./vendor/bin/phpunit
```
OR using our `composer scripts`:

_phpunit_
```bash
composer phpunit
```
- You can also run `phpcs`:

_phpcs_
```bash
composer phpcs
```

To stop hoverfly (after you are done testing):
```bash
hoverctl stop
```

Lint
----
```bash
./vendor/bin/phpstan analyse src --level 5
```

_phpstan_ using our `composer script`:
```bash
compser phpstan
```

Generate Documentation
----------------------
```bash
./vendor/bin/phpdoc -d src -t doc
```

Local Development
=================
We are managing `php environment` with [Nix](https://nixos.org/download.html "Nix Website") and [Direnv](https://direnv.net/docs/installation.html "Direnv Install page"), and we recommend downloading them before contributing to this project.
