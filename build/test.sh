#!/usr/bin/env bash

CURRENT_DIRECTORY="$(dirname "$0")"
source ${CURRENT_DIRECTORY}/.image_data.sh

SOURCE_PATH="/input"
TARGET_PATH="/output"

HOST_DATA_PATH="$(pwd)/docker/data"
HOST_SOURCE_PATH="${HOST_DATA_PATH}${SOURCE_PATH}"
HOST_TARGET_PATH="${HOST_DATA_PATH}${TARGET_PATH}"

CONTAINER_DATA_PATH="/app/data"

CONTAINER_SOURCE_PATH="${CONTAINER_DATA_PATH}${SOURCE_PATH}"
CONTAINER_TARGET_PATH="${CONTAINER_DATA_PATH}${TARGET_PATH}"
CONTAINER_NAME="test-compiler-container"
CONTAINER_PORT="8000"
HOST_PORT="9000"
CONTAINER_TEST_FILENAME="test.yml"

mkdir -p ${HOST_SOURCE_PATH}
mkdir -p ${HOST_TARGET_PATH}

cp tests/Fixtures/basil/Test/example.com.verify-open-literal.yml ${HOST_SOURCE_PATH}/${CONTAINER_TEST_FILENAME}

docker rm -f ${CONTAINER_NAME}
docker create -p ${HOST_PORT}:${CONTAINER_PORT} -v ${HOST_DATA_PATH}:${CONTAINER_DATA_PATH} --name ${CONTAINER_NAME} ${IMAGE_NAME}
docker start ${CONTAINER_NAME}

( echo "./compiler --version"; sleep 1; echo "quit"; ) | nc localhost ${HOST_PORT}
( echo "./compiler --source=${CONTAINER_SOURCE_PATH}/${CONTAINER_TEST_FILENAME} --target=${CONTAINER_TARGET_PATH}"; sleep 1; echo "quit"; ) | nc localhost ${HOST_PORT}

EXPECTED_GENERATED_FILENAME="${HOST_TARGET_PATH}/Generated8a4077150b8e96cf57e90e6bf5dd6076Test.php"
OUTPUT=$(ls ${EXPECTED_GENERATED_FILENAME} | wc -l)

if [ ${OUTPUT} != "1" ]; then
  echo "Test generation failed"
  exit 1
else
  echo "Test generation successful"
fi

rm -Rf ${HOST_DATA_PATH}

exit 0
