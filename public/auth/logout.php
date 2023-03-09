<?php

include_once(__DIR__ . '/../../lib/auth.php');

header('Access-Control-Allow-Origin: ' . get_base_url());

if (logout()) {
	header("Location: " . get_base_url());
	exit(0);
}

?>