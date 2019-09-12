<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Simple dashboard for XAMPP">
    <meta name="author" content="Mohammad Yaghobi">

    <title>XAMPP Board - <?php print(!empty($title) ? $title : "Simple Dashboard for XAMPP"); ?></title>

    <!-- Bootstrap Core CSS -->
    <link href="theme/assets/css/bootstrap.min.css" rel="stylesheet" media="screen">

    <!-- Custom CSS -->
    <link href="theme/assets/css/style.css" rel="stylesheet">

    <?php
    if (!empty($cssList)) {
        if (is_array($cssList)) {
            foreach ($cssList as $item) {
                print '<link href="theme/assets/css/' . $item . '.css" rel="stylesheet">';
            }
        } else {
            print '<link href="theme/assets/css/' . $cssList . '.css" rel="stylesheet">';
        }
    }
    ?>

    <!-- jQuery -->
    <script src="theme/assets/js/jquery.min.js"></script>

    <!-- Angular JS -->
    <script src="theme/assets/js/angular.min.js"></script>

    <!-- Bootstrap Core JavaScript -->
    <script src="theme/assets/js/bootstrap.min.js"></script>

</head>

<body>
    <!-- Navigation -->
    <nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
        <div class="container">
            <!-- Brand and toggle get grouped for better mobile display -->
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="#">XAMPP Board <sup><?php print $sys_vesrion; ?></sup></a>
            </div>
            <!-- Collect the nav links, forms, and other content for toggling -->
            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                <ul class="nav navbar-nav">
                    <li>
                        <a href="/">Apps</a>
                    </li>
                    <li>
                        <a href="/phpmyadmin">PHPMyAdmin</a>
                    </li>
                    <li>
                        <a href="phpconfig.php">PHPConfig</a>
                    </li>
                    <li>
                        <a href="phpinfo.php">PHPInfo</a>
                    </li>
                </ul>
            </div>
            <!-- /.navbar-collapse -->
        </div>
        <!-- /.container -->
    </nav>