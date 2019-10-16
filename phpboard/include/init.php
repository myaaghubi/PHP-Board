<?php


ini_set('error_reporting', E_ALL);
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');

// UTF-8 is necessary for basename()
setlocale(LC_ALL, 'UTF-8');

$sys_vesrion = "0.4.9";

define('URL_ROOT',  rtrim($_SERVER['HTTP_HOST']).'/');
define('URL_PROJECT',  URL_ROOT.trimURL($_SERVER['REQUEST_URI']).DIRECTORY_SEPARATOR);

define('DIR_PROJECT', 'phpboard');

define('PATH_ROOT', dirname(__FILE__, 3).DIRECTORY_SEPARATOR);
define('PATH_PROJECT', PATH_ROOT.DIR_PROJECT.DIRECTORY_SEPARATOR);

$project_root_files = array(
    "index.php",
    "LICENSE",
    "README.md",
);


// print "URL_ROOT > ".URL_ROOT."<br>";
// print "URL_PROJECT > ".URL_PROJECT."<br>";
// print "DIR_ROOT > ".DIR_ROOT."<br>";
// print "$[HTTP_HOST] > ".$_SERVER['HTTP_HOST']."<br>";
// print "$[REQUEST_URI] > ".$_SERVER['REQUEST_URI']."<br>";


function trimURL($url) {
    $i = strpos($url,'?d=' );
    if ($i>0) {
        $url = substr($url, 0, $i);
    }
    $url = str_replace('index.php', '', $url);
    $url = trim($url, '/');
    return $url;
}
?>
