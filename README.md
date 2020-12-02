# ShipEngine PHP

[![Build Status](https://github.com/ShipEngine/shipengine-php/workflows/shipengine-php/badge.svg)](https://github.com/ShipEngine/shipengine-php/actions)
[![Coverage Status](https://coveralls.io/repos/github/ShipEngine/shipengine-php/badge.svg?t=SkXqIE)](https://coveralls.io/github/ShipEngine/shipengine-php)

<hr />

## Test

You must have [hoverfly](https://hoverfly.io/) running in order to run tests:
```
%> hoverfly -webserver -response-body-files-path simengine > /dev/null &
```

You can now run all tests using [PHPUnit](https://phpunit.de/):
```
%> ./vendor/bin/phpunit tests
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
