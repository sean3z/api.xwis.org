<?php
header('Content-type: application/json');
header('Access-Control-Allow-Origin: *');

include 'inc/apiauth.php';
include 'inc/scrape.php';
include 'inc/database.php';
include 'inc/factions.php';

if (!isset($_GET['game'])) {
	header('HTTP/1.0 400 Bad Request');
	die('{"status": "error","code": 400,"message" : "Missing or incorrect syntax"}');
}

$game = $_GET['game'];

$method = 'pl';
if (isset($_GET['method']) && $_GET['method'] == 'clan') $method = 'cl';

$url = sprintf('http://xwis.net/%s/%s/?pure=', $game, $method);
$html = scrape($url);

foreach(explode("\n", trim($html, "\n")) as $key => $row) {
	$r = explode(' ', $row);

	// player/clan object
	$n = new stdClass();
	$n->rank = (int)$r[0];
	$n->wins = (int)$r[1];
	$n->losses = (int)$r[2];
	$n->points = (int)$r[3];
	$n->factions = (int)$r[5];

	if ($method == 'cl') $n->clan = $r[4];
	else $n->nick = $r[4];

	$n->total_games = ($n->wins + $n->losses);
	$n->percent_won = round($n->wins * 100 / ($n->wins + $n->losses));

	$ladder[$n->rank] = $n;
}
ksort($ladder);

// truncate results if desired
if (isset($_GET['mode']) && substr($_GET['mode'], 0, 3) == 'top') {
	$ladder = array_slice($ladder, 0, substr($_GET['mode'], 3), true);
}

// if player ladder, grab online information
if ($method == 'pl') {
	$url = sprintf('http://xwis.net/%s/online/?pure=', $game);
	$html = scrape($url);

	foreach(explode("\n", trim($html, "\n")) as $key => $row) {
		$r = explode("\t", $row);

		// player object
		$n = new stdClass();
		//$n->nick = $r[0];
		$n->local = (!empty($r[1]) ? $r[1] : 'In Game');
		//$n->game = $game;
		$n->clan = (isset($r[3]) ? $r[3] : '');

		$online[$r[0]] = $n;
	}

	foreach($ladder as $r => $p) {
		$ladder[$r]->online = ((isset($online[$p->nick])) ? $online[$p->nick] : 0);
	}
}

die(_json_encode($ladder));