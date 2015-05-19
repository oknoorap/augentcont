#!/bin/bash

rm -rf /etc/nginx/cache/DOMAIN
mkdir /etc/nginx/cache/DOMAIN
service nginx reload