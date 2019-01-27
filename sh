#!/usr/bin/env bash

if [ ${1} == "doc" ]; then
    cd app/Http
    ./showdoc_api.sh
elif [ ${1} == "build" ]; then
    rm -r public/css/*
    rm -r public/js/*
    cd frontend/
    # rm -r dist/*
    npm run build
    mv dist/js/* ../public/js/
    mv dist/css/* ../public/css/
    mv dist/index.html ../resources/views/outside.php
else
    cd frontend/
    npm run dev
fi
