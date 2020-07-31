#!/usr/bin/env bash

CURRENT_DIRECTORY="$(dirname "$0")"
source ${CURRENT_DIRECTORY}/.image_data.sh

mkdir -p docker/data/input
mkdir -p docker/data/output

cp tests/Fixtures/basil/Test/example.com.verify-open-literal.yml docker/data/input/test.yml

CONTAINER_NAME="test-compiler-container"
CONTAINER_PORT="8000"
HOST_PORT="8000"

docker rm -f ${CONTAINER_NAME}
docker create -p ${HOST_PORT}:${CONTAINER_PORT} -v "$(pwd)"/docker/data:/app/data --name ${CONTAINER_NAME} ${IMAGE_NAME}
docker start ${CONTAINER_NAME}

( echo "--version"; sleep 1; echo "quit"; ) | nc localhost ${HOST_PORT}
( echo "--source=/app/data/input/test.yml --target=/app/data/output"; sleep 1; echo "quit"; ) | nc localhost ${HOST_PORT}

EXPECTED_FILENAME="Generated8a4077150b8e96cf57e90e6bf5dd6076Test.php"
OUTPUT=$(docker run -v "$(pwd)"/docker/data:/app/data -it ${IMAGE_NAME} ls data/output/${EXPECTED_FILENAME} | wc -l)

if [ ${OUTPUT} != "1" ]; then
  echo "Test generation failed"
  exit 1
else
  echo "Test generation successful"
fi

rm -Rf docker/data

exit 0
