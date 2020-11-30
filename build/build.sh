#!/usr/bin/env bash

docker build -t "smartassert/basil-compiler:${TAG_NAME:-master}" .
