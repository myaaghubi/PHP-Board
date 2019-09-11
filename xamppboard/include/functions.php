<?php

// theme
function getHeader($title="", $cssList="") {
    global $sys_vesrion;
    require_once "theme/header.php";
}

// theme
function getFooter($title="", $jsList="") {
    require_once "theme/footer.php";
}

function showLabel($msg="", $type="default") {
    print '<span class="label label-'.$type.'">'.$msg.'</span>';
}

function getUserConfigParameters() {
    if (isset($_POST["config"]) and count($_POST["config"])>0) {
        $parameters = $_POST["config"];
        if (isset($parameters['name']))
            unset($parameters['name']);
    } else {
        return null;
    }
    return $parameters;
}

function saveUserConfig($path, $specificKeysTemp) {
    if (!file_exists($path)) {
        return false;
    }

    // var_dump($specificKeysTemp);

    $checkFlag = true;
    $file = fopen($path, "r"); 
    $fileFinal = "";
    $keysIndex = 0;
    while ($line = fgets($file)) {
        if (!$checkFlag or $line==PHP_EOL) {
            $fileFinal .= $line;
            continue;
        }
        if (substr($line, 0, 1)==";" or $line=="" or $line=="") {
            $fileFinal .= $line;
            continue;
        }
        $key = trim(explode("=", $line)[0]);
            // print $key." (".gettype($key).", ".strlen($key).") <br>";
        if (isset($specificKeysTemp[$key])) {
            $value = $specificKeysTemp[$key];
                // print $key.">".$value."<br>";

            $fileFinal .= $key."=".$value.PHP_EOL;

            unset($specificKeysTemp[$key]);
            if (count($specificKeysTemp)<=0) {
                $checkFlag = false;
            }
        } else {
            $fileFinal .= $line;
        }
    } 
    fclose($file); 
    // var_dump($specificKeysTemp);

    file_put_contents($path, $fileFinal);
    // var_dump($fileFinal);
}
