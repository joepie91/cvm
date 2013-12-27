#!/bin/bash
sass --watch scss/pure/:frontend/templates/pure/static/css/ > sasswatch.log 2> sasswatch.err &
echo $! > sasswatch.pid
