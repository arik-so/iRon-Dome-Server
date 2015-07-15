#!/bin/bash

echo "trying to run observer.sh " >> /tmp/observer-log

BASEDIR=$(dirname $0)
echo $BASEDIR

SCRIPTPATH="`dirname \"$0\"`" # relative path
SCRIPTPATH="`( cd \"$MY_PATH\" && pwd )`" # converting the relative path into an absolute path

# echo "`readlink -f ./`"

echo $SCRIPTPATH

COMMANDPATH="php "$SCRIPTPATH"/app/console irondome:alarms:observe"

echo $COMMANDPATH >> /tmp/observer-log
eval $COMMANDPATH