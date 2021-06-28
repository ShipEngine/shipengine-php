[![ShipEngine](https://shipengine.github.io/img/shipengine-logo-wide.png)](https://shipengine.com)

ShipEngine PHP
==============

[![Build Status](https://github.com/ShipEngine/shipengine-php/workflows/shipengine-php/badge.svg)](https://github.com/ShipEngine/shipengine-php/actions)
[![Coverage Status](https://coveralls.io/repos/github/ShipEngine/shipengine-php/badge.svg?branch=main&t=SkXqIE)](https://coveralls.io/github/ShipEngine/shipengine-php?branch=main)
[![Latest Unstable Version](https://poser.pugx.org/shipengine/shipengine/v/unstable)](//packagist.org/packages/shipengine/shipengine)
[![License](https://poser.pugx.org/shipengine/shipengine/license)](//packagist.org/packages/shipengine/shipengine)
![OS Compatibility](https://shipengine.github.io/img/badges/os-badges.svg)
> :warning: **WARNING**: This is alpha software under active development. `Caveat emptor` until a 0.1.0 release is ready.

A PHP library built on the [ShipEngine API](https://shipengine.com) offering low-level access as well as convenience methods.

</hr>

<!-- START doctoc generated TOC please keep comment here to allow auto update -->
<!-- DON'T EDIT THIS SECTION, INSTEAD RE-RUN doctoc TO UPDATE -->
**Table of Contents**

- [Quick Start](#quick-start)
- [Examples](#examples)
  - [Methods](#methods)
  - [Class Objects](#class-objects)
  - [Instantiate ShipEngine Class](#instantiate-shipengine-class)
- [Testing](#testing)
- [Linting](#linting)
- [Contributing](#contributing)

<!-- END doctoc generated TOC please keep comment here to allow auto update -->

Quick Start
===========
Install ShipEngine via [Composer](https://getcomposer.org/):
```bash
composer require shipengine/shipengine
```
- The only configuration requirement is an [API Key](https://www.shipengine.com/docs/auth/#api-keys).

> The following example assumes that you have already set the `SHIPENGIEN_API_KEY` environment variable with your Api Key using `putenv()`.

Examples
========

Methods
-------
- [validateAddress](./docs/addressValidateExample.md "Validate Address method documentation") - Indicates whether the provided address is valid. If the
  address is valid, the method returns a normalized version of the address based on the standards of the country in
  which the address resides.
- [normalizeAddress](./docs/normalizeAddressExample.md "Normalize Address method documentation") - Returns a normalized, or standardized, version of the
  address. If the address cannot be normalized, an error is returned.
- [trackPackage](./docs/trackPackageExample.md "Track Package method documentation") - Track a package by `packageId` or by `carrierCode` and `trackingNumber`. This method returns
the all tracking events for a given shipment.

Class Objects
-------------
- [ShipEngine]() - A configurable entry point to the ShipEngine API SDK, this class provides convenience methods
  for various ShipEngine API Services.

Instantiate ShipEngine Class
----------------------------
```php
<?php declare(strict_types=1);

require __DIR__ . '/vendor/autoload.php';

use ShipEngine\ShipEngine;

$apiKey = getenv('SHIPENGINE_API_KEY');

$shipengine = new ShipEngine($apiKey);
```
- You can also pass the **ShipEngine** object an **array** containing `configuration` options instead of a **string**.
```php
<?php declare(strict_types=1);

require __DIR__ . '/vendor/autoload.php';

use ShipEngine\ShipEngine;

$apiKey = getenv('SHIPENGINE_API_KEY');

$config = array(
    'apiKey' => $apiKey,
    'pageSize' => 75,
    'retries' => 3,
    'timeout' => \DateInterval('PT15S')
);

$shipengine = new ShipEngine($config);
```

Testing
=======
- You can now run all tests using [PHPUnit](https://phpunit.de/):
_phpunit_
```bash
composer test
```

Linting
=======
You can utilize the `composer` script that runs **phpcs**, **phpstan**, and **php-cs-fixer**.
```bash
composer lint
```

Contributing
============
Contributions, enhancements, and bug-fixes are welcome!  [Open an issue](https://github.com/ShipEngine/shipengine-php/issues)
on GitHub and [submit a pull request](https://github.com/ShipEngine/shipengine-php/pulls).

We are managing `php environment` with [Nix](https://nixos.org/download.html "Nix Website")
and [Direnv](https://direnv.net/docs/installation.html "Direnv Install page"), and we recommend downloading
them before contributing to this project.

- The quickest way to install Nix is to open a terminal and run the following command, make sure to follow the
  instructions output by the installation script:
  ```bash
  curl -L https://nixos.org/nix/install | sh
  ```

- Next, install `Direnv` using one of th methods outlined on their install page here:
  [Direnv Installation](https://direnv.net/docs/installation.html "Direnv Install page")

- Lastly, you will need open your terminal and while this repository the current working directory and run `direnv allow`,
  this will allow `direnv` to auto-load every time you navigate to the repo. This will automatically load the `Nix`
  environment which is running the proper version of `PHP and Xdebug (PHP 7.4)` this repository supports/requires.
  ```bash
  direnv allow
  ```
  - You will need to `cd` out of the project directory after you first install `direnv` and run `direnv allow` from within
    the project directory, and then `cd` back into the project directory for `direnv` to auto-load the `Nix` environment properly.

This project also makes use of `pre-commit hooks` to help run lint and tests at time of commit, to leverage this you will
need to install [pre-commit](https://pre-commit.com/#installation) and run the following command while in this repo:

```bash
pre-commit install
```
