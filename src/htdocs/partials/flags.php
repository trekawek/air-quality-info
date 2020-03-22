<?php foreach(\AirQualityInfo\Lib\Locale::SUPPORTED_LANGUAGES as $lang => $desc): ?>
<li>
    <a class="dropdown-item <?php echo ($lang == $currentLocale->getCurrentLang()) ? 'active' : ''; ?>" href="<?php echo $currentLocale->updateLangPrefix($_SERVER['REQUEST_URI'], $lang) ?>"><span class="flag flag-<?php echo $lang ?>"></span> <?php echo $desc ?></a>
</li>
<?php endforeach ?>