<div class="container">
<div class="row">
<div class="col-md-8 offset-md-2 text-justify">

<p class="lead">
<span class="logo">aqi.eco</span> este un proiect non-profit, dezvoltat de creatorii săi în timpul lor liber și finanțat din surse proprii. Proiectul este deschis (open source), al cărui cod este disponibil la liber pe <a href="https://github.com/trekawek/air-quality-info">GitHub</a>.
</p>

</p>
Vă rugăm să utilizați butonul de donare de mai jos pentru a sprijini infrastructura - serverul și rezervarea numelui de domeniu.
</p>
<?php if (isset(CONFIG['paypal_donate_id'])): ?>
<div class="text-center">
<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
    <input type="hidden" name="cmd" value="_donations" />
    <input type="hidden" name="business" value="<?php echo CONFIG['paypal_donate_id'] ?>" />
    <input type="hidden" name="currency_code" value="USD" />
    <input type="image" src="https://www.paypalobjects.com/en_US/PL/i/btn/btn_donateCC_LG.gif" border="0" name="submit" title="PayPal - Cel mai sigur și mai simplu mod de a plăti online! alt="Plătiți folosind PayPal" />
    <img alt="" border="0" src="https://www.paypal.com/en_PL/i/scr/pixel.gif" width="1" height="1" />
</form>
<?php endif ?>
</div>

</div>
</div>
</div>