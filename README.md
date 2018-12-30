# Air Quality Dashboard

This PHP-based page allows to display the current PM10 and PM2.5 measurements made by the Luftdaten.info-compatible device.

![Screenshot](img/screenshot.png)

## Requirements

* PHP 7.2
* php-rrd extension

## Deployment

1. Checkout or download the repository.
2. Copy the [htdocs/config-empty.php](htdocs/config-empty.php) to `htdocs/config.php`. Edit the username and password.
3. Upload the files from the [htdocs](htdocs) directoy to the web server.
4. Make sure that the web server has permissions to create and modify the RRD file configured in the `config.php`.
5. Configure the Wemos-based detector to send data to the *own API*. Use username and password the same as in the `config.php`. The path should reference the [update.php](htdocs/update.php) script.