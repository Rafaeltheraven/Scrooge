<?php
/**
 * @file
 * 
 * General useful functions that are needed in a variety of places.
 *
 * @author Rafael Dulfer
 */

include_once(__DIR__ . '/../vendor/autoload.php');

/**
 * Will send a json object to the browser with the given message and error code.
 *
 * @param mixed $message The message to send
 * @param int $code The HTTP response code to send
 *
 * @return void
 */
function return_error($message, $code) {
	return_json(array("message" => $message), $code);
}

/**
 * Sends a json object to the browser with the given array and error code.
 *
 * @param array $data The data to send
 * @param int $code The HTTP response code, defaults to 200
 *
 * @return void
 */
function return_json($data, $code=200) {
	header('Content-Type: application/json');
    http_response_code($code);
	echo json_encode($data);
}

function return_success() {
	return_json([]);
}

/**
 * Constructs the base url (I.E scrooge.tld) from the server request headers
 *
 * @return string
 */
function get_base_url() {
	$baseurl = sprintf("%s://%s",
					    isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 'https' : 'http',
					    $_SERVER['SERVER_NAME']
					);
	$server_port = $_SERVER['SERVER_PORT'];
	if ($server_port != "443" && $server_port != "80") {
		$baseurl .= ":" . $server_port;
	}
	return $baseurl;
}

function send_email(string $to, string $subject, string $msg, string $headers="", bool $html=false) {
	$config = new Config();
	if ($html) {
		$headers .= "MIME-Version: 1.0" . "\r\n";
		$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
	}
	$from = $config->get('mail_from', null);
	if (!is_null($from)) {
		$headers .= "From: " . $from . "\r\n";
	}
	mail($to, $subject, $msg, $headers);
}

?>