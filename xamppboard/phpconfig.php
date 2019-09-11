<?php

require "include/init.php";
require "include/functions.php";

getHeader("PHPConfig", "switch");

$phpIniPath = php_ini_loaded_file(); // path of php.ini

if (isset($_POST['name']) and $_POST['name'] == "config") { // detect is user saving?
    saveUserConfig($phpIniPath, getUserConfigParameters());
}


$phpIniFullConfig = parse_ini_file($phpIniPath, false, INI_SCANNER_TYPED);

$specificKeys = array(
    "max_execution_time", "max_input_time", "memory_limit", "upload_max_filesize", "post_max_size", "date.timezone", "asp_tags", "file_uploads",
    "magic_quotes_gpc", "register_globals", "zlib.output_compression", "display_errors", "allow_url_fopen", "allow_url_include"
);

$specificKeys_ = array();

for ($i = 0; $i < count($specificKeys); $i++) {
    $key = $specificKeys[$i];
    $specificKeys_[$key] = @$phpIniFullConfig[$key];
}

?>

<!-- Page Content -->
<div class="container">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">PHPConfig <br><span class="details">
                    <?php 
                        print $phpIniPath . ' ';
                        if (is_writable($phpIniPath)) {
                            showLabel("Writable", "success");
                        } else { 
                            showLabel("Not-Writable", "danger"); 
                        } 
                    ?>
                    <button value="Save" style="float: right" onclick="$('#form-config').submit()" class="btn btn-default" <?php print(is_writable($phpIniPath) ? '' : 'disabled'); ?>>Save</button>
                </span>
            </h1>
        </div>

        <div class="col-lg-12">
            <form class="form-horizontal" method="post" id="form-config">
                <div class="form-group row">
                    <input type="hidden" name="name" value="config">
                    <?php
                        $isTypeChanged = false;
                        $index = 0;
                        $prefixHtml = '<div class="col-md-6">';
                        $suffixHtml = '</div>';
                        foreach ($specificKeys_ as $key => $value) {
                            if ($value === null) {
                                continue;
                            }
                            if ($index > 1 and $index % 2 == 0) {
                                print '</div> <div class="form-group row">';
                            }
                            if (gettype($value) == "boolean") {
                                if (!$isTypeChanged) {
                                    print '</div><br> <div class="form-group row">';

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
                                    <input type="checkbox" name="config[' . $key . ']" ' . $value . '>
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
                                    ' . '<select class="form-control form-control-plaintext" name="config[' . $key . ']">'
                                    . $options .
                                    '<select> 
                                    </div>
                                    ' . $suffixHtml;
                            } else {
                                print $prefixHtml . '
                                    <label class="col-md-7 col-form-label">' . $key . '</label>
                                    <div class="col-md-5">
                                    <input type="text" class="form-control form-control-plaintext" name="config[' . $key . ']" value="' . $value . '">
                                    </div>
                                    ' . $suffixHtml;
                            }
                            $index += 1;
                        }

                        ?>
                </div>
        </div>
        </form>
    </div>
</div>
</div>
<!-- /.container -->
<script type="text/javascript">
    jQuery(document).ready(function($) {
        $("#form-config").submit(function() {
            var form = $(this);
            form.find('input[type="checkbox"]').each(function() {
                var checkbox = $(this);
                if (checkbox.is(":checked") == true) {
                    checkbox.attr('value', 'On');
                } else {
                    checkbox.prop('checked', true);
                    checkbox.attr('value', 'Off');
                }
            })
        });
    });
</script>
<?php
getFooter();
?>