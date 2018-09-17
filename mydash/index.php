<!DOCTYPE html>
<html lang="en">

<head>

	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="description" content="Simple dashboard for XAMPP">
	<meta name="author" content="Mohammad Yaghobi">

	<title>MyDash - Simple Dashboard for XAMPP</title>

	<!-- Bootstrap Core CSS -->
	<link href="assets/css/bootstrap.min.css" rel="stylesheet">

	<!-- Custom CSS -->
	<link href="assets/css/style.css" rel="stylesheet">

    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->

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
				<a class="navbar-brand" href="#">MyDash</a>
			</div>
			<!-- Collect the nav links, forms, and other content for toggling -->
			<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
				<ul class="nav navbar-nav">
					<li>
						<a href="/phpmyadmin" target="_black">PHPMyAdmin</a>
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

	<?php
	$dirs_list = "";
	$files_list = "";
	if ($handle = opendir('..')) {
		while (false !== ($entry = readdir($handle))) {
			if ($entry != "." && $entry != ".." && $entry != "xampp" && $entry != "dashboard" && $entry != "mydash") {
				if (is_dir(dirname(__FILE__, 2).'\\'.$entry))
					$dirs_list .= '
				<div class="col-lg-2 col-md-2 col-sm-3 col-xs-3 col-xxs-4 col-xxxs-6 margin-b webs">
				<a target="_blank" href="/'.$entry.'" data-href="'.$entry.'">
				<img class="img-responsive" style="padding:0 10px 3px 10px" src="assets/images/webs2.png" data-href="'.$entry.'">
				</a><br>
				<a href="/'.$entry.'" data-href="'.$entry.'">
				'.$entry.'
				</a>
				</div>';
				else
					$files_list .= '
				<div class="col-lg-1 col-md-1 col-sm-3 col-xs-3 col-xxs-4 col-xxxs-6 margin-b files">
				<div target="_blank" href="/'.$entry.'">
				<a href="/'.$entry.'" target="_blank">
				<img class="img-responsive" src="assets/images/file.png"  data-href="'.$entry.'">
				</a><br>
				<a href="/'.$entry.'" target="_blank" data-href="'.$entry.'">
				'.$entry.'
				</a>
				</div>
				</div>';
			}
		}
		closedir($handle);
	}
	?>

	<!-- Page Content -->
	<div class="container">

		<div class="row">

			<div class="col-lg-12">
				<h1 class="page-header">Your htdocs</h1>
			</div>
			<?php 
			echo $dirs_list;
			?>

		</div>

		<?php 
		if ($files_list!='') {
			echo '
			<div class="row">'
			.$files_list.'
			</div>';
		}
		?>
	</div>
	<!-- /.container -->

    <!-- Footer -->
    <footer class="py-5 bg-dark">
      <div class="container">
        <p class="m-0 text-center text-white">Copyright &copy; <a href="https://github.com/myaghobi/mydash">MyDash</a> <?php print date("Y"); ?>.</p>
      </div>
      <!-- /.container -->
    </footer>




	<div id="websContextMenu" class="dropdown clearfix">
		<ul class="dropdown-menu" role="menu" aria-labelledby="dropdownMenu" style="display:block;position:static;margin-bottom:5px;">
			<!-- <li><a tabindex="-1" rel="0" href="#">Open by Explorer</a></li> -->
			<li><a tabindex="-1" rel="1" href="#">Open by Browser</a></li>
		</ul>
	</div>
	<div id="filesContextMenu" class="dropdown clearfix">
		<ul class="dropdown-menu" role="menu" aria-labelledby="dropdownMenu" style="display:block;position:static;margin-bottom:5px;">
			<!-- <li><a tabindex="-1" rel="0" href="#">Open by Explorer</a></li> -->
			<li><a tabindex="-1" rel="1" href="#">Open by Browser</a></li>
			<!-- <li><a tabindex="-1" rel="2" href="#">Open by Editor</a></li> -->
		</ul>
	</div>




	<!-- jQuery -->
	<script src="assets/js/jquery.js"></script>

	<script>
		<?php
		echo 'var baseDir = "'.addslashes(dirname(__FILE__, 2)).'";';
		?>
		(function ($, window) {

			$.fn.contextMenu = function (a) {

				return this.each(function () {
					$(this).on("contextmenu", function (e) {
						if (e.ctrlKey) return;

						$("#websContextMenu").hide();
						$("#filesContextMenu").hide();
                //open menu
                var $menu = $(a.menuSelector)
                .data("target", e)
                .show()
                .css({
                	position: "absolute",
                	left: getMenuPosition(e.clientX, 'width', 'scrollLeft'),
                	top: getMenuPosition(e.clientY, 'height', 'scrollTop')
                })
                .off('click')
                .on('click', 'a', function (e) {
                	$menu.hide();
                	var $selectedMenu = $(e.target);

                	a.menuSelected.call(this, $menu.data("target"), $selectedMenu["context"]["rel"]);
                });
                
                return false;
            });

					$('body').click(function () {
						$("#websContextMenu").hide();
						$("#filesContextMenu").hide();
					});
				});

				function getMenuPosition(mouse, direction, scrollDir) {
					var win = $(window)[direction](),
					scroll = $(window)[scrollDir](),
					menu = $(a.menuSelector)[direction](),
					position = mouse + scroll;

					if (mouse + menu > win && menu < mouse) 
						position -= menu;

					return position;
				}    

			};
		})(jQuery, window);

		$(".webs a").contextMenu({
			menuSelector: "#websContextMenu",
			menuSelected: function (target, selectedMenu) {
				if (selectedMenu=="1") {
					window.open("\\"+target['target']['attributes']['data-href']['value']);
				} else if (selectedMenu=="0") {
					window.open("file:////127.0.0.1/"+target['target']['attributes']['data-href']['value']);
				}
				var msg = "You selected the menu item '" + selectedMenu +
				"' on the value '" + target['target']['attributes']['data-href']['value'] + "'";
				console.log(target['target']['attributes']['data-href']['value']);
				console.log(selectedMenu);
			}
		});

		$(".files a").contextMenu({
			menuSelector: "#filesContextMenu",
			menuSelected: function (target, selectedMenu) {
				if (selectedMenu=="1") {
					window.open("\\"+target['target']['attributes']['data-href']['value']);
				} else if (selectedMenu=="0") {
					window.open("file:////127.0.0.1/"+target['target']['attributes']['data-href']['value']); 
				}
				console.log(selectedMenu);
				var msg = "You selected the menu item '" + selectedMenu +
				"' on the value '" + target + "'";
				alert(msg);
			}
		});
	</script>
</body>

</html>
