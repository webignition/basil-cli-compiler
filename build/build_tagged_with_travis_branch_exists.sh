#!/usr/bin/env bash

OUTPUT=$(docker images --filter "reference=basil-compiler" | grep ${TRAVIS_BRANCH} | wc -l)

if [ ${OUTPUT} != "1" ]; then
  echo "Tagged image \"${TRAVIS_BRANCH}\" generation failed"
  exit 1
else
  echo "Tagged image \"${TRAVIS_BRANCH}\" generation successful"
fi
