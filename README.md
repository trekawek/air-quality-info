# Air Quality Dashboard

This PHP-based page allows to display the current PM10 and PM2.5 measurements made by the Luftdaten.info-compatible device.

![Screenshot](img/screenshot.png)

## Features

* Nice and clean interface
* Records stored in the RRD
* Graphs rendered with ChartJS
* No external database required
* Support for multiple devices

## Requirements

* PHP 7.2
* php-rrd extension

## Web server configuration

The web server should redirect all the requests for unknown paths to the [htdocs/index.php](htdocs/index.php). For Apache2, there's already a [.htaccess](htdocs/.htaccess) file (requires mod_rewrite). For the nginx, there's a [sample configuration available](docs/sample-nginx.conf).

## Deployment

1. Checkout or download the repository.
2. Copy the [htdocs/config-empty.php](htdocs/config-empty.php) to `htdocs/config.php`. Edit the username, password and the device id.
3. Upload the files from the [htdocs](htdocs) directory to the web server.
4. Make sure that the web server has permissions to create files in the [htdocs/data](htdocs/data) directory.
5. Configure the Wemos-based detector to send data to the *own API*. Use username and password the same as in the `config.php`. The path should reference the path: `/DEVICE_NAME/update`, where the `DEVICE_NAME` matches the one set in `config.php`.