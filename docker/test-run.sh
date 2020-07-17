#!/usr/bin/env bash

docker-compose -f docker-compose-test.yml exec compiler ./bin/compiler --source=build/source/Test/example.com.verify-open-literal.yml --target=build/target

EXPECTED_FILENAME="GeneratedF4f04d3d7255293acf2de5250e1df191Test.php"
OUTPUT=$(docker-compose -f docker-compose-test.yml exec compiler ls -I ".gitignore" build/target/${EXPECTED_FILENAME} | wc -l)

if [ $OUTPUT != "1" ]; then
  exit 1
else
  echo "Test generated successfully"
fi

docker-compose -f docker-compose-test.yml exec compiler rm /app/build/target/${EXPECTED_FILENAME}

exit 0
