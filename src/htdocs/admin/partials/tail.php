                    </div>
                </div>
            </main>
        </div>
        <footer class="site-footer">
            <div>
                <?php echo date('Y') ?> &copy; aqi.eco
            </div>
        </footer>
        <script src="https://maps.googleapis.com/maps/api/js?key=<?php echo CONFIG['google_maps_key'] ?>"></script>
        <?php echo jsLink("admin/public/js/vendor.min.js"); ?>
        <?php echo jsLink("admin/public/js/main.js"); ?>
    </body>
</html>
