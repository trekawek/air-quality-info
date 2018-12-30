<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <meta http-equiv="refresh" content="180" >
    <link rel="apple-touch-icon" href="img/dragon.png">
    <link rel="icon" type="image/png" href="img/dragon.png">
    
    <title>Jakość powietrza - Smolna 13 - wykresy</title>

    <!-- Bootstrap -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GJzZqFGwb1QTTN6wy59ffF1BuGJpLSa9DkKMp0DgiMDm4iYMj70gZWKYbI706tWS" crossorigin="anonymous">
    <link href="css/style.css" rel="stylesheet">

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
  </head>
  <body>
    <div class="container">
      <div class="row">
        <div class="col-md-12">
          <h4>Wykres dzienny</h4>
          <img src="graph.php?range=day&size=large" class="graph" />
        </div>
      </div>

      <div class="row">
        <div class="col-md-12">
          <h4>Wykres tygodniowy</h4>
          <img src="graph.php?range=week&size=large" class="graph" />
        </div>
      </div>

      <div class="row">
        <div class="col-md-12">
          <h4>Wykres miesięczny</h4>
          <img src="graph.php?range=month&size=large" class="graph" />
        </div>
      </div>

      <div class="row">
        <div class="col-md-12">
          <h4>Wykres roczny</h4>
          <img src="graph.php?range=year&size=large" class="graph" />
        </div>
      </div>
      
      <div class="row">
        <div class="col-md-12">
          <a class="btn btn-primary" href="index.php" role="button">Powrót</a>
        </div>
      </div>
      
    </div> <!-- /container -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GJzZqFGwb1QTTN6wy59ffF1BuGJpLSa9DkKMp0DgiMDm4iYMj70gZWKYbI706tWS" crossorigin="anonymous">
  </body>
</html>
