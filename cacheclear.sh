#!/bin/sh

./bin/console assets:install --symlink --relative
./bin/console cache:clear --env=prod --no-debug
./bin/console cache:clear --env=dev
./bin/console assetic:dump --env=prod --no-debug
./bin/console assetic:dump --env=dev

