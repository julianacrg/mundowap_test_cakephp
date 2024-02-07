#!/usr/bin/env bash

docker-compose exec --user $UID:$UID db mysql -u root -p