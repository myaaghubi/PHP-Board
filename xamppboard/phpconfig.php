<?php 
	require("common/common.php");

	getHeader("PHPConfig", "switch");
?>

	<?php

	$phpIniPath = php_ini_loaded_file();

	if (@$_POST['name']=="config") {
		saveKeys($phpIniPath, getPostParameters());
	}


	$phpIniKeys = parse_ini_file($phpIniPath, false , INI_SCANNER_TYPED);

	$specificKeys = array("max_execution_time", "max_input_time", "memory_limit", "upload_max_filesize", "post_max_size", "asp_tags", "file_uploads", "magic_quotes_gpc", "register_globals", "zlib.output_compression", "display_errors", "allow_url_fopen", "allow_url_include");

	$specificKeys_ = array();


	for ($i=0; $i<count($specificKeys); $i++) {
		$key = $specificKeys[$i];
		$specificKeys_[$key] = @$phpIniKeys[$key];
	}
					      

	?>

	<!-- Page Content -->
	<div class="container">
		<div class="row">
			<div class="col-lg-12">
				<h1 class="page-header">PHPConfig <br><span class="details"><?php print $phpIniPath; ?><button value="Save" style="float: right" onclick="$('#form-config').submit()" class="btn btn-default" >Save</button></span></h1>
			</div>

			<div class="col-lg-12">
				<form class="form-horizontal" method="post" id="form-config">
				    <div class="form-group row">
				    	<input type="hidden" name="name" value="config">
						<?php 
						$isTypeChanged = false;
						$index = 0;
						foreach ($specificKeys_ as $key => $value) {
							if ($value===NULL)
								continue;
							if ($index>1 and $index%2==0)
								print '</div> <div class="form-group row">';
							if (gettype($value)=="boolean") {
								if (!$isTypeChanged) {
									print '</div><br> <div class="form-group row">';

									$isTypeChanged = true;
									$index=0;
								}
								if ($value==1)
									$value = "checked";
								else
									$value = "";
								print '
									    <div class="col-md-6">
									    <label class="col-md-7 col-xs-8 col-form-label">'.$key.'</label>
									    <div class="col-md-5 col-xs-4">
											<label class="switch">
											  <input type="checkbox" name="data['.$key.']" '.$value.'>
											  <span class="slider round"></span>
											</label>											
									    </div>
									    </div>
									  ';
							} else
								print '
									    <div class="col-md-6">
									    <label class="col-md-7 col-form-label">'.$key.'</label>
									    <div class="col-md-5">
									      <input type="text" class="form-control form-control-plaintext" name="data['.$key.']" value="'.$value.'">
									    </div>
									    </div>
									  ';
							$index+=1;
						}

						// saveIniFile($phpini, $phpinipath);


						function getPostParameters() {
							if (isset($_POST["data"]) and count($_POST["data"])>1) {
								$parameters = $_POST["data"];
								if (isset($parameters['name']))
									unset($parameters['name']);
							} else
								return NULL;
							return $parameters;
						}

						function saveKeys($path, $specificKeysTemp) {
							if (!file_exists($path))
								return false;

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
									if (count($specificKeysTemp)<=0)
										$checkFlag = false;
							    } else {
									$fileFinal .= $line;
							    }
							} 
							fclose($file); 
							// var_dump($specificKeysTemp);

							file_put_contents($path, $fileFinal);
							// var_dump($fileFinal);
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
		jQuery(document).ready(function($){
			$("#form-config").submit(function () {
			    var form = $(this);
			    form.find('input[type="checkbox"]').each( function () {
			        var checkbox = $(this);
			        if (checkbox.is(":checked") == true) {
			            checkbox.attr('value','On');
			        } else {
			            checkbox.prop('checked',true);
			            checkbox.attr('value','Off');
			        }
			    })
			});
		});
	</script>
<?php 
	getFooter();
?>