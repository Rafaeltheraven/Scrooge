<?php
/**
 * @file
 * 
 * All this file does at the moment is add a nice root path to the globals.
 * If we ever need anything else that's automatically run when vendor/autoload.php is loaded
 * then we can do that here.
 * 
 * @author Rafael Dulfer
*/
$root = $_SERVER['DOCUMENT_ROOT'];
if ($root == "") {
	$root = __DIR__;
}
$root = $root . "/../";

$GLOBALS['root'] = $root;
?>