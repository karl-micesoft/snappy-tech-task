#!/usr/bin/env bash

docker compose -f compose.dev.yaml exec workspace "$@"

