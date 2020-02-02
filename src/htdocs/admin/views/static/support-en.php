<div class="container">
<div class="row">
<div class="col-md-8 offset-md-2 text-justify">

<p class="lead">
<span class="logo">aqi.eco</span> is a non-profit project, developed in the spare time and run from the private funds. It's an open-source, with its code available on <a href="https://github.com/trekawek/air-quality-info">GitHub</a>.
</p>

</p>
Please use the donate button below to support the infrastructure - server and domain name.
</p>
<?php if (isset(CONFIG['paypal_donate_id'])): ?>
<div class="text-center">
<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
    <input type="hidden" name="cmd" value="_donations" />
    <input type="hidden" name="business" value="<?php echo CONFIG['paypal_donate_id'] ?>" />
    <input type="hidden" name="currency_code" value="USD" />
    <input type="image" src="https://www.paypalobjects.com/en_US/PL/i/btn/btn_donateCC_LG.gif" border="0" name="submit" title="PayPal - The safer, easier way to pay online!" alt="Donate with PayPal button" />
    <img alt="" border="0" src="https://www.paypal.com/en_PL/i/scr/pixel.gif" width="1" height="1" />
</form>
<?php endif ?>
</div>

</div>
</div>
</div>