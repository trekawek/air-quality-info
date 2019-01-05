<?php
date_default_timezone_set('Europe/Warsaw');

$sensors = get_sensor_data($device['esp8266id']);
$max_level = null;
if (isset($device['value_mapping']['pm10'])) {
  $pm10_level = find_level(PM10_THRESHOLDS, $sensors['PM10']);
  $max_level = $pm10_level;
} else {
  $pm10_level = null;
}
if (isset($device['value_mapping']['pm25'])) {
  $pm25_level = find_level(PM25_THRESHOLDS, $sensors['PM25']);
  $max_level = $pm25_level;
} else {
  $pm25_level = null;
}
if ($pm10_level !== null && $pm25_level !== null) {
  $max_level = max($pm10_level, $pm25_level);
}
?><?php include('partials/head.php'); ?>
      <?php if ($max_level !== null): ?>
      <div class="row">
          <div class="col-md-8 offset-md-2">
          <h4>Indeks jakości powietrza</h4>
          <h4>
            <span class="badge index-cat-<?php echo $max_level; ?>"><?php echo POLLUTION_LEVELS[$max_level]['name']; ?></span>
          </h4>
          <p><?php echo POLLUTION_LEVELS[$max_level]['desc']; ?></p>
          <p><small>Źródło: <a href="http://powietrze.gios.gov.pl/pjp/content/health_informations">Główny Inspektorat Ochrony Środowiska</a></small></p>
        </div>
      </div>
      <?php else: ?>
      <div class="row">
        <div class="col-md-8 offset-md-2">
          <div class="alert alert-warning">
            Brak danych na temat zanieczyszczenia.
          </div>
        </div>
      </div>
      <?php endif ?>
      
      <div class="row">
        <div class="col-md-8 offset-md-2">
          <h4>Pomiary</h4>
          <table class="table">
            <?php if ($pm10_level !== null || $pm25_level !== null): ?>
            <thead>
              <tr>
                <th scope="col">Nazwa</th>
                <th scope="col">Wartość</th>
                <th scope="col">Indeks</th>
                <th scope="col">Procent <a href="http://ec.europa.eu/environment/air/quality/standards.htm">normy UE</a></th>
              </tr>
            </thead>
            <?php endif ?>
            <tbody>
              <?php if ($pm25_level !== null): ?>
              <tr class="index-cat-<?php echo $pm25_level; ?>">
                <th scope="row">PM<sub>2.5</sub></th>
                <td><?php echo round($sensors['PM25'], 0); ?> <small>µg/m<sup>3</sup></small></td>
                <td><?php echo POLLUTION_LEVELS[$pm25_level]['name'] ?></td>
                <td><?php echo round(100 * $sensors['PM25'] / PM25_LIMIT, 0); ?>%</td>
              </tr>
              <?php endif ?>
              <?php if ($pm10_level !== null): ?>
              <tr class="index-cat-<?php echo $pm10_level; ?>">
                <th scope="row">PM<sub>10</sub></th>
                <td><?php echo round($sensors['PM10'], 0); ?> <small>µg/m<sup>3</sup></small></td>
                <td><?php echo POLLUTION_LEVELS[$pm10_level]['name'] ?></td>
                <td><?php echo round(100 * $sensors['PM10'] / PM10_LIMIT, 0); ?>%</td>
              </tr>
              <?php endif ?>
              <tr>
                <td colspan="2">
                  <?php if ($sensors['TEMPERATURE'] !== null): ?>
                  <strong>Temperatura: </strong><?php echo round($sensors['TEMPERATURE'], 1) ?> &deg;C
                  <?php endif ?>
                </td>
                <td colspan="1">
                  <?php if ($sensors['HUMIDITY'] !== null): ?>
                  <strong>Wilgotność: </strong><?php echo round($sensors['HUMIDITY'], 0) ?>%
                  <?php endif ?>
                </td>
                <td colspan="1">
                  <?php if ($sensors['PRESSURE'] !== null): ?>
                  <strong>Ciśnienie: </strong><?php echo round($sensors['PRESSURE'], 0) ?> hPa
                  <?php endif ?>
                </td>
              </tr>
            </tbody>
          </table>
          <div class="graph-container">
            <canvas class="graph"></canvas>
          </div>
        </div>
      </div>

      <div class="row">
        <div class="col-md-8 offset-md-2 text-center">
          <div class="btn-group btn-group-sm graph-type" role="group" aria-label="Rodzaj">
            <button type="button" class="btn btn-primary" data-type="pm">PM</button>
            <button type="button" class="btn btn-secondary" data-type="temperature">Temperatura</button>
            <button type="button" class="btn btn-secondary" data-type="humidity">Wilgotność</button>
            <button type="button" class="btn btn-secondary" data-type="pressure">Ciśnienie</button>
          </div>
          <div class="btn-group btn-group-sm graph-range" role="group" aria-label="Zakres">
            <button type="button" class="btn btn-primary" data-range="day">Dzień</button>
            <button type="button" class="btn btn-secondary" data-range="week">Tydzień</button>
            <button type="button" class="btn btn-secondary" data-range="month">Miesiąc</button>
            <button type="button" class="btn btn-secondary" data-range="year">Rok</button>
          </div>
        </div>
      </div>

      <div class="row">
        <div class="col-md-8 offset-md-2">
          <small>Ostatnia aktualizacja: <?php echo date("Y-m-d H:i:s", $sensors['last_update']); ?></small>
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
        <div class="col-md-8 offset-md-2">
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
        <div class="col-md-8 offset-md-2">
          <h4>Kontakt</h4>
          <a href="mailto:tomek@rekawek.eu">Tomek Rękawek</a>
          </ul>
        </div>
      </div>

      <div class="row">
        <div class="col-md-8 offset-md-2">
          <small>Icons made by <a href="https://www.freepik.com/" title="Freepik">Freepik</a> from <a href="https://www.flaticon.com/" title="Flaticon">www.flaticon.com</a> is licensed by <a href="http://creativecommons.org/licenses/by/3.0/" title="Creative Commons BY 3.0" target="_blank">CC 3.0 BY</a></small>
        </div>
      </div>

<?php include('partials/tail.php'); ?>