#!/usr/bin/env bash

docker-compose -f docker-compose-test.yml build
docker-compose -f docker-compose-test.yml ps
