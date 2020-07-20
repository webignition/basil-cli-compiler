#!/usr/bin/env bash

REPOSITORY="basil-compiler"

DEFAULT_TAG="${TRAVIS_BRANCH:-master}"
TAG="${1:-${DEFAULT_TAG}}"

IMAGE_NAME=${REPOSITORY}:${TAG}
echo "Image name: "${IMAGE_NAME}
