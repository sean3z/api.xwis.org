<?php
header('Content-type: application/json');
header('Access-Control-Allow-Origin: *');
header('Expires: '. gmdate('D, d M Y H:i:s \G\M\T', time() + 3600));

include 'inc/apiauth.php';
include 'inc/scrape.php';
include 'inc/database.php';

if (!isset($_GET['p'])) {
	header('HTTP/1.0 400 Bad Request');
	die('{"status": "error","code": 400,"message" : "Missing or incorrect syntax"}');
}

$password = $_GET['p'];

$url = sprintf('http://xwis.net/game-account/get-nicks?p=%s', $password);
$html = scrape($url);

$html = explode("\n", trim($html, "\n"));
$nicks = array();
foreach($html as $nick) {
  $nick1 = explode(';', $nick);
  $nicks[$nick1[0]][] = $nick1[1];
}

die(_json_encode($nicks));
?>