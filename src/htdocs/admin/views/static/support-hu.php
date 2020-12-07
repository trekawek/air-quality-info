<div class="container">
<div class="row">
<div class="col-md-8 offset-md-2 text-justify">

<p class="lead">
<span class="logo">aqi.eco</span> Ez egy nonprofit projekt, amelyet készítői szabadidejükben fejlesztettek és magánforrásokból működtetnek. A projekt nyílt forráskódú, amelynek kódja itt érhető el: <a href="https://github.com/trekawek/air-quality-info">GitHub</a>.
</p>

</p>
Kérjük, az adományozás gomb használatával támogassa a projektünket a domain név, és a kiszolgáló szerver fenntartásához.
</p>
<?php if (isset(CONFIG['paypal_donate_id'])): ?>
<div class="text-center">
<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
    <input type="hidden" name="cmd" value="_donations" />
    <input type="hidden" name="business" value="<?php echo CONFIG['paypal_donate_id'] ?>" />
    <input type="hidden" name="currency_code" value="USD" />
    <input type="image" src="https://www.paypalobjects.com/en_US/PL/i/btn/btn_donateCC_LG.gif" border="0" name="submit" title="PayPal - Biztonságos, egyszerű online fizetés!" alt="Támogatás PayPal használatával" />
    <img alt="" border="0" src="https://www.paypal.com/en_PL/i/scr/pixel.gif" width="1" height="1" />
</form>
<?php endif ?>
</div>

<p>Projektünk támogatója:</p>
<p>
  <a href="https://www.digitalocean.com/">
    <img src="https://opensource.nyc3.cdn.digitaloceanspaces.com/attribution/assets/SVG/DO_Logo_horizontal_blue.svg" width="201px">
  </a>
</p>

</div>
</div>
</div>
