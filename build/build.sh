#!/usr/bin/env bash

CURRENT_DIRECTORY="$(dirname "$0")"
source ${CURRENT_DIRECTORY}/.image_data.sh

docker build -f docker/Dockerfile -t ${IMAGE_NAME} .