<?php

/**
 * @file
 * 
 * An easy to import script to get a connector the the database.
 * 
 * @author Rafael Dulfer
 */

require_once(__DIR__ . '/../../vendor/autoload.php');
require_once($root . 'lib/utils.php');

$debug = false;
$config = new Config($root . 'config/config.ini.php');
$server_type = $config->get('db_type', 'pgsql');
$db_server = $config->get('db_host', 'localhost');
$db_port = $config->get('db_port', '5432');
$db_name = $config->get('db_name');
$db_user = $config->get('db_user');
$db_password = $config->get('db_password');
try {
    $conn = new PDO("$server_type:host=$db_server;port=$db_port;dbname=$db_name", $db_user, $db_password);
    if ($debug) {
        echo "<p>Connected to the DB</p>";
    }
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $conn->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
} catch (PDOException $e) {
	return_error("An error has occured while connection to the db: " . $e->getMessage(), 500);
    $conn = false; // IF conn is false something is seriously wrong and everything needs to stop.
}