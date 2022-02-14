#!/bin/bash

for i in {1..1000} ; do
echo ' -o /dev/null http://www.dv-campus-checklist.local/ -:'
done | xargs curl -s
