<!-- aqi.eco widget BEGIN -->
<script>
(function() {
    var eventMethod = window.addEventListener ? "addEventListener" : "attachEvent";
    var eventer = window[eventMethod];
    var messageEvent = eventMethod === "attachEvent" ? "onmessage" : "message";
    eventer(messageEvent, function(e) {
        if (e.data.hasOwnProperty("aqi-widget") && e.data['aqi-widget'] == <?php echo $widgetId ?>) {
            var iframe = document.getElementById("aqi-widget-<?php echo $widgetId ?>");
            if (iframe) {
                iframe.style.height = e.data.frameHeight + "px";
            }
        }
    });
})();
</script>
<?php if ($widgetTemplate == 'horizontal'): ?>
    <iframe id="aqi-widget-<?php echo $widgetId ?>" style="width: 1110px; height: 210px; border: 0;" src="<?php echo $widgetUri ?>"></iframe>
<?php else: ?>
    <iframe id="aqi-widget-<?php echo $widgetId ?>" style="width: 300px; height: 620px; border: 0;" src="<?php echo $widgetUri ?>"></iframe>
<?php endif ?>
<!-- aqi.eco widget END -->
