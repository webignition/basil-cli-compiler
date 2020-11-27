#!/usr/bin/env bash

docker build -f docker/Dockerfile -t "smartassert/basil-compiler:${TAG_NAME:-master}" .
