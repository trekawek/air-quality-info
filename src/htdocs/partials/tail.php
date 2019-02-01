  </div>
  <link rel="stylesheet" href="/public/css/themes/<?php echo $current_theme ?>.min.css"/>
  <link rel="stylesheet" href="/public/css/vendor.min.css"/>
  <link rel="stylesheet" href="/public/css/style.css?v=15"/>
  <script>
if ('serviceWorker' in navigator) {
    console.log("registering");
  window.addEventListener('load', function() {
    navigator.serviceWorker.register('/sw.js');
  });
}
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
