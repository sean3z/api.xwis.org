<?php

if (false) {
	$auth = $_GET['apikey'];
	$apikeys = array('_____', 'a667e3e3dc70');
	
	header('HTTP/1.0 403 Forbidden');
	die('{"status": "error","code": 403,"message" : Account Inactive"}');
}