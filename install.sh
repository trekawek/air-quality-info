#!/bin/bash

export DEBIAN_FRONTEND="noninteractive"

echo "Installing packages..."
apt-get update -qq
apt-get install -qq nginx php-fpm php-rrd curl zip net-tools

echo "Creating nginx config..."
cat <<EOF > /etc/nginx/sites-enabled/default 
server {
    listen 80;

	server_name default_server;
	root /var/www/air-quality-info;
	index index.php;

    location / {
            try_files \$uri \$uri/ /index.php?\$args;
    }

    location ~ \.php$ {
            fastcgi_intercept_errors on;
            # this probably should be updated
            fastcgi_pass unix:/var/run/php/php7.0-fpm.sock;
            include fastcgi.conf;
    }
}
EOF

echo "Downloading Air Quality Info..."
mkdir -p /var/www/air-quality-info
curl -L https://github.com/trekawek/air-quality-info/archive/master.zip > /tmp/air-quality-info.zip

echo "Unpacking the htdocs"
unzip -q /tmp/air-quality-info.zip -d /tmp
mv /tmp/air-quality-info-master/htdocs/* /var/www/air-quality-info

echo "Configuring Air Quality Info..."
read -e -p    'Enter username: ' username
read -e -s -p 'Enter password: ' password
echo
read -e -p    'Enter sensor id: ' sensor_id

cat <<EOF > /var/www/air-quality-info/config.php
<?php
define('CONFIG', array(
  'devices' => array(
    array(
        'user'        => '${username}',
        'password'    => '${password}',
        'esp8266id'   => '${sensor_id}',
        'name'        => 'main',          # this will be used in URLs
        'description' => 'Main location', # user-friendly location name, will be used in navbar
    ),
  ),
# Whether to store the last received JSON dump.
  'store_json_payload' => true,
# Google Analytics ID
  'ga_id' => ''
));
?>
EOF

chown -R www-data:www-data /var/www/air-quality-info

echo "Starting PHP and nginx..."
/etc/init.d/php7.0-fpm start
/etc/init.d/nginx start

server_ip="$(ifconfig | grep -Eo 'inet (addr:)?([0-9]*\.){3}[0-9]*' | grep -Eo '([0-9]*\.){3}[0-9]*' | grep -v '127.0.0.1')"

echo "Installation is ready"
echo ""
echo "Page is available at http://${server_ip}"
echo ""
echo "Configuration:"
echo "Server: ${server_ip}"
echo "Path: /main/update"
echo "Port: 80"
echo "User: ${username}"
echo "Password: [redacted]"