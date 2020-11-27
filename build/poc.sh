#!/usr/bin/env bash

CURRENT_DIRECTORY=$(dirname "$0")

echo $CURRENT_DIRECTORY

#sh ./.image_data.sh

#CURRENT_DIRECTORY="$(dirname "$0")"
#
#echo ${CURRENT_DIRECTORY}

#declare -a STEPS=(
#  ${CURRENT_DIRECTORY}"/build.sh"
#  ${CURRENT_DIRECTORY}"/test_image_exists.sh"
#  ${CURRENT_DIRECTORY}"/test.sh"
#)
#
#for STEP in "${STEPS[@]}"; do
#  ${STEP}
#
#  if [ $? -ne 0 ]
#  then
#    echo ${STEP}" failed"
#
#    exit $?
#  fi
#done

echo "foo 02"

exit 0
