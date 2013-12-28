<?php

function scrape($url) {
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_HEADER, 0); 
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
	curl_setopt($ch, CURLOPT_ENCODING, '');
	curl_setopt($ch, CURLOPT_USERAGENT, 'XWIS API Scraper/2.0'); 
	$buffer = curl_exec($ch);
	curl_close($ch);
	if (empty($buffer)) {
		header('HTTP/1.0 504 Gateway Time-out');
		die('{"status": "error","code": 504,"message" : "Gateway Timeout"}');
	}
	if (preg_match('/404 - Not Found/', $buffer) || preg_match('/400 - Bad Request/', $buffer)) {
		header('HTTP/1.0 404 Not Found');
		die('{"status": "error","code": 404,"message" : "Entity Not Found"}');
	}
	return $buffer;
}

function _json_encode($data) {
	$data = json_encode($data);
	if (isset($_GET['callback'])) $data = $_GET['callback'] .'('. $data .')';
	return $data;
}