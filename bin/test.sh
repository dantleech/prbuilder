#!/bin/bash

curl -X POST -H 'Content-Type: application/json' -d @tests/data/pull-request-event.json http://localhost:8090/index.php/pr
