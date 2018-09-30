<?php 
	require("common/common.php");

	getHeader();
?>

	<?php
	$dirs_list = "";
	$files_list = "";
	if ($handle = opendir('..')) {
		while (false !== ($entry = readdir($handle))) {
			if ($entry != "." && $entry != ".." && $entry != "xampp" && $entry != "dashboard" && $entry != "xamppboard") {
				if (is_dir(dirname(__FILE__, 2).'\\'.$entry))
					$dirs_list .= '
				<div class="col-lg-2 col-md-2 col-sm-3 col-xs-3 col-xxs-4 col-xxxs-6 margin-b webs">
				<a target="_blank" href="/'.$entry.'" data-href="'.$entry.'">
				<img class="img-responsive" src="theme/assets/images/webs2.png" data-href="'.$entry.'">
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
				<img class="img-responsive" src="theme/assets/images/file.png"  data-href="'.$entry.'">
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
				<h1 class="page-header">Your htdocs <br><span class="details"><?php print apache_get_version(); ?></h1>

			</div>
			<?php 
			print $dirs_list;
			?>

		</div>

		<?php 
		if ($files_list!='') {
			print '
			<div class="row">'
			.$files_list.'
			</div>';
		}
		?>
	</div>
	<!-- /.container -->


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

<?php 
	getFooter();
?>