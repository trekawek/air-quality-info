  <div class="row">
    <div class="col-md-8 offset-md-2">
      <hr>
      <?php if (isset($domainTemplate['footer'])): ?>
      <?php echo $domainTemplate['footer'] ?>
      <?php endif ?>
    </div>
    <div class="col-md-8 offset-md-2 text-muted text-center">
      <small>
        <?php echo __('Powered by ') ?><a href="https://aqi.eco">aqi.eco</a>.
      </small>
    </div>
  </div>

  </div>

  <link rel="stylesheet" href="/public/css/themes/<?php echo $currentTheme->getTheme() ?>.min.css"/>
  <link rel="stylesheet" href="/public/css/vendor.min.css?v=32"/>
  <link rel="stylesheet" href="/public/css/style.css?v=33"/>
  <?php if (isset($domainTemplate['css'])): ?>
  <?php echo "<style>\n".$domainTemplate['css']."\n</style>\n" ?>
  <?php endif ?>
<?php if (!isset(CONFIG['enable_pwa']) || CONFIG['enable_pwa'] === true): ?>
  <script>
if ('serviceWorker' in navigator) {
    console.log("registering");
  window.addEventListener('load', function() {
    navigator.serviceWorker.register('/sw.js');
  });
}
<?php endif ?>
  </script>
  <template id="spinner">
    <div class="row">
        <div class="col-md-8 offset-md-2 text-center">
            <p></p>
            <div class="spinner-border text-primary" role="status">
                <span class="sr-only"><?php echo __('Loading...')?></span>
            </div>
        </div>
    </div>
  </template>
  </body>
</html>
