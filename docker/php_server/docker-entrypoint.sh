#!/bin/sh -e

php-fpm -D
nginx -g "daemon off;"
