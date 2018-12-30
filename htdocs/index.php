<?php
require('config.php');
require('constants.php');
date_default_timezone_set('Europe/Warsaw');

$data = rrd_lastupdate($rrd_file);
$ts = $data['last_update'];
$sensors = array();
for ($i = 0; $i < $data['ds_cnt']; $i++) {
  $sensors[$data['ds_navm'][$i]] = $data['data'][$i];
}

function fmt_nmbr($number) {
  return number_format($number, 0, ',', ' ');
}

function find_index($index, $value) {
  foreach ($index as $i => $v) {
    if ($v > $value) {
      return $i - 1;
    }
  }
  return count($index) - 1;
}

$pm10_index = find_index(PM10_INDEX, $sensors['PM10']);
$pm25_index = find_index(PM25_INDEX, $sensors['PM25']);
$max_index = max($pm10_index, $pm25_index);

$rel_pm10 = 100 * $sensors['PM10'] / PM10_LIMIT;
$rel_pm25 = 100 * $sensors['PM25'] / PM25_LIMIT;

?><!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <meta http-equiv="refresh" content="180" >
    <link rel="apple-touch-icon" href="img/dragon_white_background.png">
    <link rel="icon" type="image/png" href="img/dragon.png">

    <title>Jakość powietrza</title>

    <!-- Bootstrap -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GJzZqFGwb1QTTN6wy59ffF1BuGJpLSa9DkKMp0DgiMDm4iYMj70gZWKYbI706tWS" crossorigin="anonymous">
    <link href="css/style.css" rel="stylesheet">

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
  </head>
  <body>
    <div class="container">
      <div class="row">
          <div class="col-md-6 offset-md-3">
          <h4>Indeks jakości powietrza</h4>
          <h4>
            <span class="badge index-cat-<?php echo $max_index; ?>"><?php echo INDEX_DESC[$max_index][0] ?></span>
          </h4>
          <p><?php echo INDEX_DESC[$max_index][1]; ?></p>
          <p><small>Źródło: <a href="http://powietrze.gios.gov.pl/pjp/content/health_informations">Główny Inspektorat Ochrony Środowiska</a></small></p>
        </div>
      </div>
      
      <div class="row">
        <div class="col-md-6 offset-md-3">
          <h4>Pomiary</h4>
          <table class="table">
            <thead>
              <tr>
                <th scope="col">Nazwa</th>
                <th scope="col">Wartość</th>
                <th scope="col">Indeks</th>
                <th scope="col">Procent <a href="http://ec.europa.eu/environment/air/quality/standards.htm">normy UE</a></th>
              </tr>
            </thead>
            <tbody>
              <tr class="index-cat-<?php echo $pm25_index; ?>">
                <th scope="row">PM<sub>2.5</sub></th>
                <td><?php echo fmt_nmbr($sensors['PM25']); ?> <small>µg/m<sup>3</sup></small></td>
                <td><?php echo INDEX_DESC[$pm25_index][0]; ?></td>
                <td><?php echo fmt_nmbr($rel_pm25); ?>%</td>
              </tr>
              <tr class="index-cat-<?php echo $pm10_index; ?>">
                <th scope="row">PM<sub>10</sub></th>
                <td><?php echo fmt_nmbr($sensors['PM10']); ?> <small>µg/m<sup>3</sup></small></td>
                <td><?php echo INDEX_DESC[$pm10_index][0]; ?></td>
                <td><?php echo fmt_nmbr($rel_pm10); ?>%</td>
              </tr>
            </tbody>
          </table>
          <img src="graph.php" class="graph" />
          <p><small><a href="all_graphs.php">Zobacz wszystkie wykresy</a></small></p>

          <p>Ostatnia aktualizacja: <?php echo date("Y-m-d H:i:s", $ts); ?></p>
        </div>
      </div>

      <!--div class="row">
          <div class="col-md-6 offset-md-3 apps">
            <h4>Aplikacje na telefon</h4>
            <p>
              Zadziała także dowolna inna aplikacja pobierająca informacje z serwisu <a href="https://luftdaten.info/pl/dom">Luftdaten</a>. Przykładowe aplikacje:
            </p>
            <p>
              <a href="https://itunes.apple.com/de/app/breathe-luftqualit%C3%A4tsmonitor/id1355513543?mt=8"><img src="img/ios_link.svg"/></a>
              <a href="https://play.google.com/store/apps/details?id=com.mrgames13.jimdo.feinstaubapp"><img src="img/android_link.svg"/></a>
            </p>
          </div>
      </div-->

      <div class="row">
        <div class="col-md-6 offset-md-3">
          <h4>Informacje</h4>
          <ul>
            <li><a href="https://nettigo.pl/products/nettigo-air-monitor-kit-0-2-1-zbuduj-wlasny-czujnik-smogowy">Informacja o użytym detektorze smogu</a></li>
            <!--li><a href="https://maps.luftdaten.info/#13/52.4247/16.9684">Mapa smogu w Poznaniu</a></li>
            <li><a href="https://luftdaten.info/pl/dom/">Projekt Luftdaten</a></li-->
            <li><a href="https://github.com/trekawek/air-quality-info">Kod źródłowy</a></li>
          </ul>
        </div>
      </div>

      <div class="row">
        <div class="col-md-6 offset-md-3">
          <h4>Kontakt</h4>
          <a href="mailto:tomek@rekawek.eu">Tomek Rękawek</a>
          </ul>
        </div>
      </div>

      <div class="row">
        <div class="col-md-6 offset-md-3">
          <small>Icons made by <a href="https://www.freepik.com/" title="Freepik">Freepik</a> from <a href="https://www.flaticon.com/" title="Flaticon">www.flaticon.com</a> is licensed by <a href="http://creativecommons.org/licenses/by/3.0/" title="Creative Commons BY 3.0" target="_blank">CC 3.0 BY</a></small>
        </div>
      </div>

    </div> <!-- /container -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GJzZqFGwb1QTTN6wy59ffF1BuGJpLSa9DkKMp0DgiMDm4iYMj70gZWKYbI706tWS" crossorigin="anonymous">
  </body>
</html>
