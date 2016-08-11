#!/bin/sh

./app/console assets:install --symlink --relative
./app/console cache:clear --env=prod --no-debug 
./app/console cache:clear --env=dev
./app/console assetic:dump --env=prod --no-debug
./app/console assetic:dump --env=dev

