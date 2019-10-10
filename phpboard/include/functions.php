<?php

function ajaxDone($status = true, $msg = "done", $array = null) {
    $out = array();
    $out["status"] = $status;
    $out["msg"] = $msg;
    if ($array != null) {
        $out["data"] = $array;
    }

    exit(json_encode($out));
}

function ajaxInvalid() {
    $out = array();
    $out["status"] = "0";
    $out["msg"] = "Invalid Request!";

    exit(json_encode($out));
}

// theme
function getHeader($title = "", $cssList = "") {
    global $sys_vesrion;
    require_once "theme/header.php";
}

// theme
function getFooter($title = "", $jsList = "") {
    require_once "theme/footer.php";
}

function showLabel($msg = "", $type = "default") {
    print '<span class="label label-' . $type . '">' . $msg . '</span>';
}

function makeLabel($msg = "", $type = "default") {
    return '<span class="label label-' . $type . '">' . $msg . '</span>';
}

function showBreadCrumb($address = '') {
    $exp = explode('/', $address);
    $str = '<ul class="breadcrumb">';
    $str .= '<li><a href="http://' . URL_PROJECT . '">root</a></li>';
    $dir = '';
    foreach ($exp as $item) {
        if (empty($item)) {
            continue;
        }
        if (!empty($dir)) {
            $dir .= '/';
        }
        $dir .= $item;
        $str .= '<li><a href="http://' . URL_PROJECT . '?d=' . $dir . '">' . $item . '</a></li> ';
    }
    $str .= '</ul>';

    print $str;
}

function getWebServerDetails() {
    $str = $_SERVER["SERVER_SOFTWARE"];
    if (!empty($_SERVER['SERVER_SOFTWARE'])) {
        $str = strpos($str, "PHP")!==false?$str:$str.' '.'PHP/' . PHP_VERSION;
    }

    $str = str_replace('+',' ', $str); // it's space in XAMPP and + in LEMP, so I need to consider both. 
    $exp = explode(' ', $str);
    $str = '';
    $count = 0;
    foreach ($exp as $item) {
        $str .= makeLabel($item) . ' ';
        if ($count++ >= 3) {
            break; // SERVER_SOFTWARE sometimes contine some unkown values in lemp
        }
    }

    return $str;
}

function getFileSizeFormatted($size) {
    $units = array('Bytes', 'KB', 'MB', 'GB', 'TB');
    $power = $size > 0 ? floor(log($size, 1024)) : 0;
    return number_format($size / pow(1024, $power), 2, '.', ',') . ' ' . $units[$power];
}

function getUserConfigParameters() {
    if (isset($_POST["config"]) and count($_POST["config"]) > 0) {
        $parameters = $_POST["config"];
        if (isset($parameters['name']))
            unset($parameters['name']);
    } else {
        return null;
    }
    return $parameters;
}

function removeDirFile($path) {
    if (is_dir($path)) {
        $files = array_diff(scandir($path), ['.', '..']);
        foreach ($files as $file) {
            removeDirFile("$path/$file");
        }
        rmdir($path);
    } else {
        return unlink($path);
    }
    return true;
}

function zipDir($path) {
    $path = rtrim($path, '/');
    $dirTarget = basename($path);
    $directory = str_replace($dirTarget, '', $path);
    $directory = str_replace(DIR_ROOT, '', $directory);

    $zip = new ZipArchive;
    $download = $dirTarget . '.zip';
    $zip->open(DIR_ROOT.$directory.$download, ZipArchive::CREATE);

    addDirToZip($zip, $dirTarget, DIR_ROOT . $directory.$dirTarget);

    $zip->close();

    return true;
}

function addDirToZip($zip, $dirname, $location, $locationLocal='') {
    if (!file_exists($location)) {
        die("maybe file/folder path incorrect: ");
    }

    $locationLocal.=$dirname.'/';

    $dirname .= '/';
    $location = rtrim($location, '/').'/';
    $dir = opendir($location); // Read all Files in Dir

    while ($file = readdir($dir)) {
        if ($file == '.' || $file == '..') continue;

        if (filetype($location . $file) == 'dir') {
            addDirToZip($zip, $file, $location.$file, $locationLocal);
        } else {
            $zip->addFile($location . $file, $locationLocal . $file);
        }
    }
}

function saveUserConfig($path, $specificKeysTemp) {
    if (!file_exists($path)) {
        return "error";
    }

    // var_dump($specificKeysTemp);

    $checkFlag = true;
    $file = fopen($path, "r");
    $fileFinal = "";
    $keysIndex = 0;
    while ($line = fgets($file)) {
        if (!$checkFlag or $line == PHP_EOL) {
            $fileFinal .= $line;
            continue;
        }
        if (substr($line, 0, 1) == ";" or $line == "" or $line == "") {
            $fileFinal .= $line;
            continue;
        }
        $key = trim(explode("=", $line)[0]);
        // print $key." (".gettype($key).", ".strlen($key).") <br>";
        if (isset($specificKeysTemp[$key])) {
            $value = $specificKeysTemp[$key];
            // print $key.">".$value."<br>";

            $fileFinal .= $key . "=" . $value . PHP_EOL;

            unset($specificKeysTemp[$key]);
            if (count($specificKeysTemp) <= 0) {
                $checkFlag = false;
            }
        } else {
            $fileFinal .= $line;
        }
    }
    fclose($file);
    // var_dump($specificKeysTemp);

    file_put_contents($path, $fileFinal);
    return "done";
    // var_dump($fileFinal);
}