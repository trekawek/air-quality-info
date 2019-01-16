#!/bin/bash -e

cd /opt/air-quality-info

# mysql CLI client variables
export MYSQL_HOST=db
export MYSQL_PWD="${MYSQL_PASSWORD}"
export HTDOCS=htdocs

MYSQL_DATABASE="air_quality_info"

mysql -uroot -p"${MYSQL_ROOT_PASSWORD}" -e "DROP DATABASE IF EXISTS ${MYSQL_DATABASE}; CREATE DATABASE ${MYSQL_DATABASE}; GRANT ALL PRIVILEGES ON ${MYSQL_DATABASE}.* TO '${MYSQL_USER}'@'%';"
mysql "${MYSQL_DATABASE}" < mysql-schema.sql

for rrd in htdocs/data/*.rrd; do
  php -d memory_limit=256M migrate-rrd-to-mysql.php "${rrd}"
done