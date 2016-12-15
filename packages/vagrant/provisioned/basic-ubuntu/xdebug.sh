#!/bin/bash

# Start and stop XDebug nicely

if [ "$1" = "start" ]; then
    echo "Starting XDebug..."
    sudo sed -i 's/^#zend_ext/zend_ext/g' /etc/php5/fpm/conf.d/20-xdebug.ini
elif [ "$1" = "stop" ]; then
    echo "Stopping XDebug..."
    sudo sed -i 's/^zend_ext/#zend_ext/g' /etc/php5/fpm/conf.d/20-xdebug.ini
else
    echo "Invalid argument. Please use \"start\" or \"stop\""
    exit 1
fi

sudo service php5-fpm restart