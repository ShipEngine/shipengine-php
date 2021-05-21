#!/bin/bash

sed -i'' -e "s/VERSION = '.*'/VERSION = '$TAG'/" src/ShipEngine.php
php -l src/ShipEngine.php
