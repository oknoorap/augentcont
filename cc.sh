#!/bin/bash

sudo rm -rf /etc/nginx/cache
sudo mkdir /etc/nginx/cache
sudo service nginx reload