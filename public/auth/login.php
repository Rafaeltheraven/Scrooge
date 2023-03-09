<?php
include_once(__DIR__ . '/../../lib/auth.php');

header('Access-Control-Allow-Origin: ' . get_base_url());

if (empty($_POST)) {
	return_error("Can only access this endpoint using proper forms.", 400);
} else if (!isset($_POST['username'])) {
	return_error("Did not provide a username.", 400);
} else if (!isset($_POST['password'])) {
	return_error("Did not provide a password.", 400);
} else {
	if (login($_POST['username'], $_POST['password'])) {
		header("Location: " . get_base_url());
		exit(0);
	}
}

?>