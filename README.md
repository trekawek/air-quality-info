# Air Quality Info

This PHP-based page allows to display the current PM10 and PM2.5 measurements made by the [Sensor.Community](https://sensor.community)-compatible device.

![Screenshot](img/screenshot-en.png)

[Live demo](http://mosina.aqi.eco/)

This is the code of the SaaS product now, available live on https://aqi.eco.

## Features

* Nice and clean interface
* Records stored in MySQL
* Graphs rendered with ChartJS
* Support for multiple devices
* Locale support
* Progressive Web App

## Setup

```
mv config-DEFAULT.php config.php
docker-compose up
```

is enough to start the project. The admin dashboard will be available under: http://aqi.eco.localhost:8080/, while the actual Air Quality Info pages will use http://NAME.aqi.eco.localhost:8080 naming schema.

## Resources

* [Nettigo Air Monitor](https://air.nettigo.pl/)
* [Sensor.Community](https://sensor.community)
* [Forum thread on the app](https://forum.kodujdlapolski.pl/t/strona-informacyjna-dla-czujnika-luftdaten/3217/35) (in Polish)

<p>This project is supported by:</p>
<p>
  <a href="https://www.digitalocean.com/">
    <img src="https://opensource.nyc3.cdn.digitaloceanspaces.com/attribution/assets/SVG/DO_Logo_horizontal_blue.svg" width="201px">
  </a>
</p>
