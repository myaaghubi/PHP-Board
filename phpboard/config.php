<?php

require "include/init.php";
require "include/functions.php";
// exit;

$phpIniPath = php_ini_loaded_file(); // path of php.ini
$isPhpIniWirtable = is_writable($phpIniPath);

if (isset($_POST['name']) and $_POST['name'] == "config") { // detect is user saving?
    print saveUserConfig($phpIniPath, getUserConfigParameters());
    exit;
}

getHeader("PHPConfig", "switch");

$phpIniFullConfig = parse_ini_file($phpIniPath, false, INI_SCANNER_TYPED);

$specificKeys = array(
    "max_execution_time", "max_input_time", "memory_limit", "post_max_size", "session.gc_maxlifetime", "upload_max_filesize", "max_file_uploads",
    "date.timezone", "mysqli.default_port", "default_charset", "safe_mode", "asp_tags", "file_uploads", "magic_quotes_gpc", "register_globals",
    "zlib.output_compression", "display_errors", "allow_url_fopen", "allow_url_include"
);

$specificKeys_ = array();

for ($i = 0; $i < count($specificKeys); $i++) {
    $key = $specificKeys[$i];
    $specificKeys_[$key] = @$phpIniFullConfig[$key];
}

?>

<!-- Page Content -->
<div class="container" ng-app="configApp" ng-controller="configCtrl">
    <div class="container">
        <div class="row">
            <h1 class="page-header" style="position: relative;">PHPConfig</h1>
        </div>
        <div class="row align-items-center justify-content-between mb-3">
            <span class="details">
                <?php
                print $phpIniPath . ' ';
                if (is_writable($phpIniPath)) {
                    showLabel("Writable", "success");
                } else {
                    showLabel("Not-Writable", "danger");
                }
                ?>
            </span>
            <button type="button" id="save-btn" style="float: right" ng-click="save()" class="btn btn-<?php print($isPhpIniWirtable ? 'primary' : 'default'); ?>" <?php print($isPhpIniWirtable ? '' : 'disabled'); ?>>
                <span id="msg-block-spinner" class="spinner-grow spinner-grow-sm spinner-hidden" style="margin:0 5px 0 -2px;" role="status" aria-hidden="true"></span>Save
            </button>
        </div>
    </div>
    <form class="form-horizontal" method="post" id="form-config">
        <div class="form-group row justify-content-between">
            <input type="hidden" name="name" ng-model="data.name" value="config">
            <?php
            $isTypeChanged = false;
            $index = 0;
            $prefixHtml = '<div class="col-6 row">';
            $suffixHtml = '</div>';
            foreach ($specificKeys_ as $key => $value) {
                if ($value === null) {
                    continue;
                }
                if ($index > 1 and $index % 2 == 0) {
                    print '</div> <div class="form-group row justify-content-between">';
                }
                if (gettype($value) == "boolean") {
                    if (!$isTypeChanged) {
                        print '</div><br> <div class="form-group row justify-content-between">';

                        $isTypeChanged = true;
                        $index = 0;
                    }
                    if ($value == 1) {
                        $value = "checked";
                    } else {
                        $value = "";
                    }
                    print $prefixHtml . '
                                <label class="col-md-7 col-xs-8 col-form-label">' . $key . '</label>
                                <div class="col-md-5 col-xs-4">
                                <label class="switch">
                                <input type="hidden" name="config[' . $key . ']" value="Off">
                                <input type="checkbox" name="config[' . $key . ']" value="On" ' . $value . '>
                                <span class="slider round"></span>
                                </label>
                                </div>
                                ' . $suffixHtml;
                } else if ($key == 'date.timezone') {
                    $tzlist = DateTimeZone::listIdentifiers(DateTimeZone::ALL);
                    $options = '';
                    foreach ($tzlist as $item) {
                        $selection = '';
                        if ($value == $item) {
                            $selection = 'selected';
                        }
                        $options .= '<option value="' . $item . '" ' . $selection . '>' . $item . '</option>';
                    }
                    print $prefixHtml . '
                                <label class="col-md-7 col-form-label">' . $key . '</label>
                                <div class="col-md-5">
                                ' . '<select class="form-control" name="config[' . $key . ']">'
                        . $options .
                        '<select>
                                </div>
                                ' . $suffixHtml;
                } else {
                    print $prefixHtml . '
                                <label class="col-md-7 col-form-label">' . $key . '</label>
                                <div class="col-md-5">
                                <input type="text" class="form-control" name="config[' . $key . ']" value="' . $value . '">
                                </div>
                                ' . $suffixHtml;
                }
                $index += 1;
            }

            ?>
        </div>
    </form>

</div>
</div>
<script>
    var form = angular.element('#form-config');
    var saveBtn = angular.element('#save-btn');
    var msgBlockSpinner = angular.element('#msg-block-spinner');
    var msgBlockMsg = angular.element('#msg-block-msg');
    angular.module('configApp', []).controller('configCtrl', function($scope, $http, $timeout) {
        saveBtn.removeClass().addClass('btn btn-primary');
        $scope.save = function(angular) {
            saveBtn.attr('disabled', true);
            msgBlockSpinner.removeClass('spinner-hidden');
            saveBtn.removeClass().addClass('btn btn-warning');

            $http({
                method: 'POST',
                url: "phpconfig.php",
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                data: form.serialize()
            }).then(function successCallback(response) {
                $timeout(function() {
                    saveBtn.attr('disabled', false);
                    saveBtn.removeClass().addClass('btn btn-primary');
                    msgBlockSpinner.addClass('spinner-hidden');
                }, 500);
            }, function errorCallback(response) {
                $timeout(function() {
                    saveBtn.attr('disabled', false);
                    saveBtn.removeClass().addClass('btn btn-danger');
                    msgBlockSpinner.addClass('spinner-hidden');
                }, 500);
            });
        };
    });
</script>
<!-- /.container -->
<?php
getFooter();
?>
