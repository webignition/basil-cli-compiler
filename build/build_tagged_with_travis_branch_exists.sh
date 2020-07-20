#!/usr/bin/env bash

CURRENT_DIRECTORY="$(dirname "$0")"
source ${CURRENT_DIRECTORY}/.image_data.sh

OUTPUT=$(docker images --filter "reference=${IMAGE_REPOSITORY}" | grep ${TRAVIS_BRANCH} | wc -l)

if [ ${OUTPUT} != "1" ]; then
  echo "Tagged image \"${TRAVIS_BRANCH}\" generation failed"
  exit 1
else
  echo "Tagged image \"${TRAVIS_BRANCH}\" generation successful"
fi
