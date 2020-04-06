<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Simple dashboard for PHP">
    <meta name="author" content="Mohammad Yaghobi">

    <title>PHP-Board - <?php print(!empty($title) ? $title : "Simple Dashboard for PHP"); ?></title>

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
    <nav class="navbar navbar-expand-md navbar-dark bg-dark fixed-top">
        <div class="container">
            <a class="navbar-brand" href="#">PHP-Board <sup><?php print $sys_vesrion; ?></sup></a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="/">Files</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="config.php">Config</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/phpmyadmin">PHPMyAdmin</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="phpinfo.php">PHPInfo</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
