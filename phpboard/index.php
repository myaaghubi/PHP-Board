<?php

require "include/init.php";
require "include/functions.php";

$pData = file_get_contents('php://input', 'r');
if (!empty($pData)) {
    $pData = json_decode($pData);
    $act = $pData->act ?? '';
    if ($act == 'del') {
        $path = $pData->path ?? '';
        if (!empty($path) and file_exists(PATH_ROOT . $path)) {
            ajaxDone(removeDirFile(PATH_ROOT . $path));
        } else {
            ajaxDone(false);
        }
    } else if ($act == 'zip') {
        $path = $pData->path ?? '';
        if (!empty($path) and file_exists(PATH_ROOT . $path)) {
            ajaxDone(zipDir(PATH_ROOT . $path));
        } else {
            ajaxDone(false);
        }
    } else if ($act == 'addFile') {
        $directory = $pData->dir ?? '';
        $name = $pData->name ?? '';
        if (empty($name)) {
            ajaxDone(false, "File name is empty!");
        }
        if (file_exists(PATH_ROOT . $directory . $name)) {
            ajaxDone(false, "Duplicated file name!");
        } else {
            ajaxDone(fopen(PATH_ROOT . $directory . $name, "w") !== false);
        }
    } else if ($act == 'addFolder') {
        $directory = $pData->dir ?? '';
        $name = $pData->name ?? '';
        if (empty($name)) {
            ajaxDone(false, "Folder name is empty!");
        }
        if (file_exists(PATH_ROOT . $directory . $name)) {
            ajaxDone(false, "Duplicated folder name!");
        } else {
            ajaxDone(mkdir(PATH_ROOT . $directory . $name, 0777, true), "w");
        }
    }

    ajaxInvalid();
} else if (isset($_POST['upload'])) {
    $path = $_POST['path'];
    foreach ($_FILES as $file) {
        @move_uploaded_file($file["tmp_name"], PATH_ROOT . $path . $file["name"]);
    }
    ajaxDone(true);
}



$directory = '';
if (!empty($_GET['d'])) {
    $directory .= $_GET['d'] . '/';
}

if (@$_GET['act'] == 'download') {
    if ($_GET['targetf'] ?? false) {
        $targetFile = $_GET['targetf'];
        if (file_exists(PATH_ROOT . $directory . $targetFile)) {
            header("Content-Type:application/octet-stream");
            header("Accept-Ranges: bytes");
            header("Content-Length: " . filesize(PATH_ROOT . $directory . $targetFile));
            header("Content-Disposition: attachment; filename=" . $targetFile);
            readfile(PATH_ROOT . $directory . $targetFile);
            exit;
        }
    } else if ($_GET['targetd'] ?? false) {
        $targetDir = $_GET['targetd'];
        $zip = new ZipArchive;
        $download = $targetDir . '.zip';
        $zip->open($download, ZipArchive::CREATE);

        addDirToZip($zip, $targetDir, PATH_ROOT . $directory . $targetDir);

        $zip->close();
        header('Content-Type: application/zip');
        header("Content-Disposition: attachment; filename = $targetDir.zip");
        header('Content-Length: ' . filesize($download));
        header("Location: $download");
        exit;
    }
    exit("<script>window.close()</script>");
}

$isInProjectPath = strpos(PATH_PROJECT, PATH_ROOT . $directory) !== false && strlen($directory) > 0;
$isInRootDir = PATH_ROOT . $directory == PATH_ROOT ? true : false;

$dirs_list = "";
$files_list = "";
if ($files = array_diff(scandir(PATH_ROOT . $directory), ['.', '..'])) {
    $index = 0;
    foreach ($files as $entry) {
        if ($entry != "." && $entry != "..") {
            $path =  PATH_ROOT . $directory . $entry;
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
                    . (true ? '<button type="button" class="btn btn-outline-secondary btn-sm btn-dirtozip" ng-click="zipDir($event, ' . $index . ')">Create Zip File</button> ' : ' ')
                    . makeDeleteButtonForDir($path, $entry, $index) .
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
                    . (true ? '<input type="button" class="btn btn-outline-secondary btn-sm" ng-click="downloadFile($event, ' . $index . ')" value="Download"> ' : ' ')
                    . makeDeleteButtonForFile($path, $entry, $index) .
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
    return decoct(fileperms($path) & 0777) . '<br>' . implode('+', $desc);
}

function makeDeleteButtonForDir($path, $entry, $index)
{
    global $isInProjectPath, $isInRootDir;
    if (!is_writable($path)) {
        return '<input type="button" class="btn btn-danger btn-sm" value="Delete" title="Permission Denied!" disabled="disabled"> ';
    } else if ($isInProjectPath || ($isInRootDir && $entry == DIR_PROJECT)) {
        return '<input type="button" class="btn btn-danger btn-sm" value="Delete" title="Not Allowed!" disabled="disabled"> ';
    }
    return '<input type="button" class="btn btn-danger btn-sm" ng-click="deleteFileDir($event, ' . $index . ')" value="Delete" title="Delete without confirmation!"> ';
}

function makeDeleteButtonForFile($path, $entry, $index)
{
    global $project_root_files, $isInProjectPath, $isInRootDir;
    if (!is_writable($path)) {
        return '<input type="button" class="btn btn-danger btn-sm" value="Delete" title="Permission Denied!" disabled="disabled"> ';
    } else if ($isInProjectPath || ($isInRootDir && in_array($entry, $project_root_files))) {
        return '<input type="button" class="btn btn-danger btn-sm" value="Delete" title="Not Allowed!" disabled="disabled"> ';
    }
    return '<input type="button" class="btn btn-danger btn-sm" ng-click="deleteFileDir($event, ' . $index . ')" value="Delete" title="Delete without confirmation!"> ';
}

getHeader();
?>
<!-- Page Content -->
<div class="container">
    <div class="row" ng-app="theApp">
        <div class="col-lg-12">
            <h1 class="page-header">Your Host <br><span class="details"><?php print getWebServerDetails(); ?>
            </h1>
            <?php
            showBreadCrumb($directory);
            ?>
            <div class="file-upload">
                <ul class="row p-0 my-1" ng-controller="addNewCtl">
                    <div class="col col-auto pr-0">
                        <li type="button" class="btn btn-outline-primary btn-sm" ng-click="showUploadBox($event)">Upload</li>
                    </div>
                    <div class="col col-auto pr-0 btn-group">
                        <button type="button" id="btn-newFile" class="btn btn-outline-primary btn-sm" ng-click="addNewFile($event)">New File</button>
                        <button type="button" id="btn-newFolder" class="btn btn-outline-primary btn-sm" ng-click="addNewFolder($event)">New Folder</button>
                    </div>
                    <div id="upload-group" class="col input-group input-group-sm" style="display:none">
                        <input type="text" ng-model="newfileName" id="newfileName" class="form-control" name="newfileName" placeholder="File/Folder Name">
                        <div class="input-group-append">
                            <button class="input-group-text btn btn-danger" type="button" ng-click="addNewFileFolderHide($event)">Cancel</button>
                            <button class="input-group-text btn btn-success" type="button" ng-click="addNewFileFolder($event)">Add</button>
                        </div>
                    </div>
                </ul>
                <upload id="filedrop" to="index.php"></upload>
            </div>
            <div class="table-responsive" style="border: 1px solid #efefef;">
                <table class="table table-hover">
                    <thead class="thead-light">
                        <tr>
                            <th>Name</th>
                            <th>Size</th>
                            <th>Modified</th>
                            <th>Permissions</th>
                            <th style="min-width: 180px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody ng-controller="filesCtl">
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
    var directory = "<?php print $directory; ?>";
</script>
<script src="theme/assets/js/app.js"></script>
<?php
getFooter();
?>
