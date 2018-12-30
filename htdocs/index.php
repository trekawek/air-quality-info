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

function parse_rgb($hex) {
  return sscanf($hex, "%02x%02x%02x");
}

function find_index($index, $value) {
  if ($value == 0) {
    return array(0, 0);
  }
  foreach ($index as $i => $v) {
    if ($v >= $value) {
      return array($i - 1, $i);
    }
  }
  return array(count($index) - 1, count($index) - 1);
}

function pm_color($index, $value) {
  list($i1, $i2) = find_index($index, $value);
  $x1 = $index[$i1];
  $x2 = $index[$i2];
  $rgb1 = parse_rgb(INDEX_DESC[$i1][0]);
  $rgb2 = parse_rgb(INDEX_DESC[$i2][0]);
  if ($x1 == $x2) {
    $x = 0;
  } else {
    $x = ($value - $x1) / ($x2 - $x1);
  }
  $rgb = array(
    (($rgb2[0] - $rgb1[0]) * $x + $rgb1[0]),
    (($rgb2[1] - $rgb1[1]) * $x + $rgb1[1]),
    (($rgb2[2] - $rgb1[2]) * $x + $rgb1[2])
  );
  return "rgba(${rgb[0]}, ${rgb[1]}, ${rgb[2]}, 0.5)";
}

list($pm10_index) = find_index(PM10_INDEX, $sensors['PM10']);
list($pm25_index) = find_index(PM25_INDEX, $sensors['PM25']);

$max_index = max($pm10_index, $pm25_index);
$index_desc = INDEX_DESC[$max_index];

$rel_pm10 = 100 * $sensors['PM10'] / 50;
$rel_pm25 = 100 * $sensors['PM25'] / 25;

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
            <span class="badge" style="color: #<?php echo $index_desc[1] ?>; background-color: #<?php echo $index_desc[0] ?>;"><?php echo $index_desc[2] ?></span>
          </h4>
          <p><?php echo $index_desc[3]; ?></p>
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
              <tr style="background-color: <?php echo pm_color(PM25_INDEX, $sensors['PM25']); ?>;">
                <th scope="row">PM<sub>2.5</sub></th>
                <td><?php echo fmt_nmbr($sensors['PM25']); ?> <small>µg/m<sup>3</sup></small></td>
                <td><?php echo INDEX_DESC[$pm25_index][2]; ?></td>
                <td><?php echo fmt_nmbr($rel_pm25); ?>%</td>
              </tr>
              <tr style="background-color: <?php echo pm_color(PM10_INDEX, $sensors['PM10']); ?>;">
                <th scope="row">PM<sub>10</sub></th>
                <td><?php echo fmt_nmbr($sensors['PM10']); ?> <small>µg/m<sup>3</sup></small></td>
                <td><?php echo INDEX_DESC[$pm10_index][2]; ?></td>
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
