#!/bin/sh

set -e

IMAGE_TAG="demo"

docker build . --file docker/Dockerfile -t "$IMAGE_TAG"
docker run -p8000:8000 -t "$IMAGE_TAG"
