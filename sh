#!/usr/bin/env bash

if [ ${1} == "doc" ]; then
    cd app/Http
    ./showdoc_api.sh
else
    cd frontend/
    npm run dev
fi
