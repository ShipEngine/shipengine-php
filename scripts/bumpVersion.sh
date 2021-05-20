#!/bin/bash

CLEANED_TAG=${TAG//[-._]/}
MAJOR=${CLEANED_TAG:0:1}
MINOR=${CLEANED_TAG:1:1}
PATCH=${CLEANED_TAG:2:2}

sed -i'' -e "s/VERSION = '.*'/VERSION = '$TAG'/" src/ShipEngine.php
php -l src/ShipEngine.php

sed -i'' -e "s/MAJOR = .*/MAJOR = $MAJOR;/" src/Util/VersionInfo.php
sed -i'' -e "s/MINOR = .*/MINOR = $MINOR;/" src/Util/VersionInfo.php
sed -i'' -e "s/PATCH = .*/PATCH = $PATCH;/" src/Util/VersionInfo.php
php -l src/Util/VersionInfo.php
