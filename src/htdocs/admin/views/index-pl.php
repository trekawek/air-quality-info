<!DOCTYPE html>
<html lang="en">
    <head>
        <?php if (CONFIG['ga_id']): ?>
        <script async src="https://www.googletagmanager.com/gtag/js?id=<?php echo CONFIG['ga_id']; ?>"></script>
        <script>
window.dataLayer = window.dataLayer || [];
function gtag(){dataLayer.push(arguments);}
gtag('js', new Date());
gtag('config', '<?php echo CONFIG['ga_id']; ?>');
        </script>
        <?php endif; ?>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
        <title>aqi.eco</title>
        <link rel="stylesheet" href="/public/css/vendor.min.css"/>
        <link rel="stylesheet" href="/public/css/themes/default.min.css"/>
        <link rel="stylesheet" href="/admin/public/css/landing.css"/>
    </head>

<body>
    <div class="container">

    <nav class="navbar navbar-light bg-light">
        <div class="navbar-brand">
            <img src="/public/img/aqi.png"/><span class="logo">aqi.eco</span>
        </div>
        
        <div class="my-2 my-sm-0">
            <a href="<?php echo l('user', 'login') ?>" class="btn btn-success" role="button" >Zaloguj się</a>
            <a href="<?php echo l('user', 'register') ?>" class="btn btn-primary">Zarejestruj się</a>
        </div>
    </nav>

    <div class="px-3 py-3 pt-md-5 pb-md-4 mx-auto">

        <h1 class="display-4 logo text-center">aqi.eco</h1>

        <p class="lead text-justify">
        <span class="logo">aqi.eco</span> to serwis, dzięki któremu w atrakcyjny sposób wyświetlisz wyniki pomiarów czystości
        powietrza, zmierzone urządzeniami <a href="https://air.nettigo.pl/">Nettigo Air Monitor</a> lub <a href="https://luftdaten.info/#pll_switcher">Luftdaten</a>.
        </p>

        <p class="text-center">
        <a class="btn btn-warning" href="https://smolna.aqi.eco/">Przykładowa strona</a>
        </p>

        <p class="text-justify">
        Zarejestruj się w serwisie, skonfiguruj swój czujnik i podziel się adresem utworzonej strony z sąsiadami i rodziną, aby wszyscy mogli kontrolować
        jakość powietrza w Waszej okolicy.
        </p>

        <p class="text-justify">
        Wskazania Twojego czujnika będą świetnie wyglądać zarówno w przeglądarce jak i na telefonie.
        </p>

        <p class="text-center">
        <img src="/admin/public/img/screenshot-en.png" width="100%"/>
        </p>
    </div>

    <footer class="pt-4 my-md-5 pt-md-5 border-top text-center">
        &copy; 2019 Tomasz Rękawek
    </footer>
</body>
</html>