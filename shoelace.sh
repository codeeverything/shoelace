#! /bin/bash

if [ $1 = "create-project" ]; then
    mkdir .shoelace-project
    cd .shoelace-project
    composer create-project $2
    find . -maxdepth 1 -exec mv {} .. \;
    cd ..
    rmdir .shoelace-project
fi