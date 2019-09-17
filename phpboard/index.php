<?php

require "include/init.php";
require "include/functions.php";

$pData = file_get_contents('php://input', 'r');
if (!empty($pData)) {
    $pData = json_decode($pData);
    $act = $pData->act ?? '';
    if ($act == 'del') {
        $path = $pData->path ?? '';
        if (!empty($path) and file_exists(DIR_ROOT . $path)) {
            ajaxDone(removeDirFile(DIR_ROOT . $path));
        } else {
            ajaxDone(false);
        }
    } else { }

    ajaxInvalid();
}



$directory = '';
if (!empty($_GET['d'])) {
    $directory .= $_GET['d'] . '/';
}

if (@$_GET['act'] == 'download') {
    if ($_GET['file'] ?? false) {
        $downloadfile = $_GET['file'];
        if (file_exists(DIR_ROOT.$directory.$downloadfile)) {
            header ("Content-Type:application/octet-stream");
            header ("Accept-Ranges: bytes");
            header ("Content-Length: ".filesize(DIR_ROOT.$directory.$file));
            header ("Content-Disposition: attachment; filename=".$file);
            readfile(DIR_ROOT.$directory.$file);
            exit;
        }
    }
    exit("<script>window.close()</script>");
}



$dirs_list = "";
$files_list = "";
if ($files = array_diff(scandir(DIR_ROOT . $directory), ['.', '..'])) {
    $index = 0;
    foreach ($files as $entry) {
        if ($entry != "." && $entry != "..") {
            $path =  DIR_ROOT . $directory . $entry;
            // print decoct(fileperms($file) & 0777);
            $stat = stat($path);
            if (is_dir($path)) {
                $dirs_list .= '
                <tr id="tr-' . $index . '" data-href="' . $directory . $entry . '">
                    <td>
                        <img class="dir" src="theme/assets/images/folder.png"> 
                        <a href="http://' . URL_PROJECT . '?d=' . $directory . $entry . '" data-href="' . $entry . '"> ' . $entry . ' </a> 
                    </td>
                    <td> --- </td>
                    <td>' . date('M d, Y H:i', $stat['mtime']) . '</td>
                    <td>' . getPermDescription($path) . '</td>
                    <td>'
                    . (true ? '<input type="button" ng-click="downloadDir($event, ' . $index . ')" value="download"> ' : ' ')
                    . (is_writable($path) ? '<input type="button" ng-click="deleteFileDir($event, ' . $index . ')" value="delete"> ' : ' ') .
                    '</td>
				</tr>';
            } else {
                $files_list .= '
                <tr id="tr-' . $index . '" data-href="' . $directory . $entry . '" data-file="' . $entry . '">
                    <td>
                        <img class="file" src="theme/assets/images/file.png">
                        <a href="http://' . URL_ROOT . $directory . $entry . '" data-href="' . $entry . '"> ' . $entry . ' </a> 
                    </td>
                    <td>' . getFileSizeFormatted($stat['size']) . '</td>
                    <td>' . date('M d, Y H:i', $stat['mtime']) . '</td>
                    <td>' . getPermDescription($path) . '</td>
                    <td>'
                    . (true ? '<input type="button" ng-click="downloadFile($event, ' . $index . ')" value="download"> ' : ' ')
                    . (is_writable($path) ? '<input type="button" ng-click="deleteFileDir($event, ' . $index . ')" value="delete"> ' : ' ') .
                    '</td>
                </tr>';
            }
            $index += 1;
        }
    }
}

function getPermDescription($path)
{
    $desc = array();
    if (is_readable($path))
        $desc[] = 'read';
    if (is_writable($path))
        $desc[] = 'write';
    if (is_executable($path))
        $desc[] = 'exec';
    return implode('+', $desc);
}



getHeader();
?>

<!-- Page Content -->
<div class="container">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">Your Host <br><span class="details"><?php print getWebServerDetails(); ?>
            </h1>
            <?php
            showBreadCrumb($directory);
            ?>
            <div class="table-responsive" style="border: 1px solid #efefef;">
                <table class="table filestable">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Size</th>
                            <th>Modified</th>
                            <th>Permissions</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody ng-app="filesApp" ng-controller="filesCtl">
                        <?php
                        if (empty($dirs_list) and empty($files_list)) {
                            print '<tr>
                                    <td colspan="5" style="text-align: center;
                                    padding: 50px;">Directory is empty!</td>
                                   <tr>';
                        } else {
                            print $dirs_list . $files_list;
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
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
    echo 'var baseDir = "' . addslashes(dirname(__FILE__, 2)) . '";';
    ?>
        (function($, window) {

            $.fn.contextMenu = function(a) {

                return this.each(function() {
                    $(this).on("contextmenu", function(e) {
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
                            .on('click', 'a', function(e) {
                                $menu.hide();
                                var $selectedMenu = $(e.target);

                                a.menuSelected.call(this, $menu.data("target"), $selectedMenu["context"]["rel"]);
                            });

                        return false;
                    });

                    $('body').click(function() {
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
        menuSelected: function(target, selectedMenu) {
            if (selectedMenu == "1") {
                window.open("\\" + target['target']['attributes']['data-href']['value']);
            } else if (selectedMenu == "0") {
                window.open("file:////127.0.0.1/" + target['target']['attributes']['data-href']['value']);
            }
            var msg = "You selected the menu item '" + selectedMenu +
                "' on the value '" + target['target']['attributes']['data-href']['value'] + "'";
            console.log(target['target']['attributes']['data-href']['value']);
            console.log(selectedMenu);
        }
    });

    $(".files a").contextMenu({
        menuSelector: "#filesContextMenu",
        menuSelected: function(target, selectedMenu) {
            if (selectedMenu == "1") {
                window.open("\\" + target['target']['attributes']['data-href']['value']);
            } else if (selectedMenu == "0") {
                window.open("file:////127.0.0.1/" + target['target']['attributes']['data-href']['value']);
            }
            console.log(selectedMenu);
            var msg = "You selected the menu item '" + selectedMenu +
                "' on the value '" + target + "'";
            alert(msg);
        }
    });
</script>
<script>
    var rootUrl = "<?php print URL_ROOT; ?>";
    angular.module('filesApp', [])
        .controller('filesCtl', function($scope, $http, $element, $timeout, $window) {
            $scope.downloadFile = function(event, id) {
                var trItem = $element.find('#tr-' + id);
                var path = trItem.attr("data-href");
                var file = trItem.attr("data-file");
                var url = window.location.href;
                url+=(url.search('/?')>0?'&':'?');

                var mw = $window.open(url+"act=download&file=", '_blank');
                mw.blur();
setTimeout($window.focus(), 0);
$window.focus();
            }
            $scope.deleteFileDir = function(event, id) {
                var trItem = $element.find('#tr-' + id);
                var path = trItem.attr("data-href");
                trItem.removeClass();
                $http({
                    method: 'DELETE',
                    url: "index.php",
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    data: {
                        act: 'del',
                        path: path
                    }
                }).then(function successCallback(response) {
                    console.log(response.data);
                    if (response.data.status === true) {
                        trItem.hide(250);
                        $timeout(function() {
                            trItem.remove();
                        }, 250);
                    } else {
                        trItem.addClass("dangerHighlight");
                        $timeout(function() {
                            trItem.addClass("highlightOut");
                        }, 1000);
                    }

                }, function errorCallback(response) {

                });
            }

        });
</script>
<?php
getFooter();
?>