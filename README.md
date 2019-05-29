# Air Quality Info

This PHP-based page allows to display the current PM10 and PM2.5 measurements made by the Luftdaten.info-compatible device.

![Screenshot](img/screenshot-en.png)

[Live demo](http://smolna.aqi.eco/)

This is the code of the SaaS product now, available live on https://aqi.eco.

## Features

* Nice and clean interface
* Records stored in the RRD or MySQL
* Graphs rendered with ChartJS
* No external database required
* Support for multiple devices
* Locale support
* Progressive Web App

## Setup

```
docker-compose up
```

is enough to start the project. The admin dashboard will be available under: http://aqi.eco.localhost:8080/, while the actual Air Quality Info pages will use http://NAME.aqi.eco.localhost:8080 naming schema.

## Resources

* [Nettigo Air Monitor](https://air.nettigo.pl/)
* [Luftdaten](https://luftdaten.info/)
* [Forum thread on the app](https://forum.kodujdlapolski.pl/t/strona-informacyjna-dla-czujnika-luftdaten/3217/35) (in Polish)