<?php if ($this->name !== null): ?>
<input type="hidden" name="_form_name" value="<?php echo $this->name ?>"/>
<?php endif ?>
<input type="hidden" name="csrf_token" value="<?php echo \AirQualityInfo\Lib\CsrfToken::getToken() ?>"/>