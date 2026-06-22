#!/bin/bash -e

docker build ./docker-img/dev -t aqi-dev && docker run -ti --rm -v $(pwd):/work aqi-dev /bin/sh
