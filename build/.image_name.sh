#!/usr/bin/env bash

REPOSITORY="basil-compiler"

if [ -z "$1" ]; then
  TAG="latest"
else
  TAG=$1
fi

IMAGE_NAME=$REPOSITORY:$TAG
echo "Image name: "$IMAGE_NAME
