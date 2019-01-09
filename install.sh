#!/bin/bash

export DEBIAN_FRONTEND="noninteractive"

echo "Installing packages..."
apt-get update -qq
apt-get install -qq nginx php-fpm php-rrd curl zip net-tools > /dev/null

echo "Discovering server IP..."
server_ip="$(ifconfig | grep -Eo 'inet (addr:)?([0-9]*\.){3}[0-9]*' | grep -Eo '([0-9]*\.){3}[0-9]*' | grep -v '127.0.0.1')"
echo "It's ${server_ip}"

if [ -e /etc/nginx/sites-available/air-quality-info ]; then
    echo "nginx config already exists, skipping..."
else
    read -e -p "Enter the site domain (or press Enter to just use IP ${server_ip}): " domain

    echo "Creating nginx config..."
    cat <<EOF > /etc/nginx/sites-available/air-quality-info
server {
    listen 80;

    server_name ${domain:-default_server};
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
    if [ -z "${domain}" ]; then
        rm /etc/nginx/sites-enabled/default
    fi
fi

if [ ! -L /etc/nginx/sites-enabled/air-quality-info ]; then
    echo "Linking the nginx config..."
    ln -s /etc/nginx/sites-available/air-quality-info /etc/nginx/sites-enabled
fi

domain="$(grep server_name /etc/nginx/sites-enabled/air-quality-info | awk '{ print $2 }' | tr -d ';')"
if [ "${domain}" == 'default_server' ]; then
    domain="${server_ip}"
fi

echo "Downloading Air Quality Info..."
curl -L https://github.com/trekawek/air-quality-info/archive/master.zip > /tmp/air-quality-info.zip

if [ -d /var/www/air-quality-info ]; then
    echo "Moving the old installation"
    mv /var/www/air-quality-info /tmp/air-quality-info.old
fi

echo "Unpacking the htdocs"
unzip -q /tmp/air-quality-info.zip -d /tmp
mv /tmp/air-quality-info-master/htdocs /var/www/air-quality-info
rm -rf /tmp/air-quality-info-master

if [ -d /tmp/air-quality-info.old ]; then
    echo "Restoring config and data"
    mv /tmp/air-quality-info.old/data/* /var/www/air-quality-info/data
    mv /tmp/air-quality-info.old/config.php /var/www/air-quality-info/config.php
    rm -rf /tmp/air-quality-info.old
fi

if [ -e /var/www/air-quality-info/config.php ]; then
    echo "Air Quality Info config already exists, skipping..."
else
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
fi

chown -R www-data:www-data /var/www/air-quality-info

echo "Starting PHP and nginx..."
/etc/init.d/php7.0-fpm start
/etc/init.d/nginx start

echo "Installation is ready"
echo ""
echo "Page is available at http://${domain}"
echo ""
echo "Configuration:"
echo "Server: ${domain}"
echo "Path: /main/update"
echo "Port: 80"
echo "User: ${username}"
echo "Password: [redacted]"