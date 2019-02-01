<?php include('partials/head.php'); ?>
<p></p>
<div class="row">
    <div class="col-md-8 offset-md-2">
        <h4>Informacje</h4>
        <p>Strona oparta jest o aplikację <a href="https://github.com/trekawek/air-quality-info">Air Quality Info</a>.</p>
        <ul>
            <li><a href="<?php echo l($device, 'debug'); ?>">Diagnostyka detektora</a></li>
            <?php if (isset($device['contact_email'])): ?>
            <li><?php echo __('Contact info') ?>:
                <a href="mailto:<?php echo $device['contact_email'] ?>">
                <?php echo $device['contact_name'] ?>
                </a>
            </li>
            <?php endif ?>
        </ul>
    </div>
</div>

<div class="row">
    <div class="col-md-8 offset-md-2">
        <small>Icons made by <a href="https://www.freepik.com/" title="Freepik">Freepik</a> from <a href="https://www.flaticon.com/" title="Flaticon">www.flaticon.com</a> is licensed by <a href="http://creativecommons.org/licenses/by/3.0/" title="Creative Commons BY 3.0" target="_blank">CC 3.0 BY</a></small>
    </div>
</div>
<?php include('partials/tail.php'); ?>