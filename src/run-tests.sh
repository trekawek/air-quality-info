#!/bin/bash -e

cd /opt/air-quality-info

# mysql CLI client variables
export MYSQL_HOST=db
export MYSQL_PWD="${MYSQL_PASSWORD}"

# test variables
export HTDOCS=htdocs

MYSQL_DATABASE="air_quality_info_test_${RANDOM}"

mysql -uroot -p"${MYSQL_ROOT_PASSWORD}" -e "CREATE DATABASE ${MYSQL_DATABASE}; GRANT ALL PRIVILEGES ON ${MYSQL_DATABASE}.* TO '${MYSQL_USER}'@'%';"
mysql "${MYSQL_DATABASE}" < mysql-schema.sql

php tests/mysql_dao_test.php

mysql -e "DROP DATABASE ${MYSQL_DATABASE}"