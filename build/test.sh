#!/usr/bin/env bash

CURRENT_DIRECTORY="$(dirname "$0")"
source ${CURRENT_DIRECTORY}/.image_name.sh

mkdir -p docker/data/input
mkdir -p docker/data/output

cp tests/Fixtures/basil/Test/example.com.verify-open-literal.yml docker/data/input/test.yml

docker run -v "$(pwd)"/docker/data:/app/data -it ${IMAGE_NAME} ./bin/compiler --source=/app/data/input/test.yml --target=/app/data/output

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
