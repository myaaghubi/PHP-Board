<?php
	if (!empty($_SERVER['HTTPS']) && ('on' == $_SERVER['HTTPS'])) {
		$uri = 'https://';
	} else {
		$uri = 'http://';
	}
	$uri .= $_SERVER['HTTP_HOST'];
	// header('Location: '.$uri.'/dashboard/'); // *, uncomment * lines to get classic dashboard!
	// exit; // *
	header('Location: '.$uri.'/xamppboard/');
	exit;
?>
Something is wrong with the XAMPP installation :-(
