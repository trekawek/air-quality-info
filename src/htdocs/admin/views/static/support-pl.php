<p class="lead">
<span class="logo">aqi.eco</span> jest projektem niekomercyjnym, rozwijanym w wolnym czasie i utrzymywanym ze środków prywatnych. Środki przekazane za pomocą przycisku będą wykorzystane do utrzymania serwera i domeny.
</p>
<?php if (isset(CONFIG['paypal_donate_id'])): ?>
<div class="text-center">
<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
    <input type="hidden" name="cmd" value="_donations" />
    <input type="hidden" name="business" value="<?php CONFIG['paypal_donate_id'] ?>" />
    <input type="hidden" name="currency_code" value="PLN" />
    <input type="image" src="https://www.paypalobjects.com/pl_PL/PL/i/btn/btn_donateCC_LG.gif" border="0" name="submit" title="PayPal - The safer, easier way to pay online!" alt="Przekaż darowiznę za pomocą przycisku PayPal" />
    <img alt="" border="0" src="https://www.paypal.com/pl_PL/i/scr/pixel.gif" width="1" height="1" />
</form>
<?php endif ?>
</div>