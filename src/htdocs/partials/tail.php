  <div class="row">
    <div class="col-md-8 offset-md-2 text-muted text-center">
      <hr>
      <small>
        <?php echo __('Powered by ') ?><a href="https://aqi.eco">aqi.eco</a>.
      </small>
    </div>
  </div>

  </div>

  <link rel="stylesheet" href="/public/css/themes/<?php echo $currentTheme->getTheme() ?>.min.css"/>
  <link rel="stylesheet" href="/public/css/vendor.min.css"/>
  <link rel="stylesheet" href="/public/css/style.css?v=19"/>
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
