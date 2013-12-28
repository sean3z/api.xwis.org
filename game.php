<?php

header('Content-type: application/json');
header('Access-Control-Allow-Origin: *');
header('Expires: '. gmdate('D, d M Y H:i:s \G\M\T', time() + 3600));

include 'inc/apiauth.php';
include 'inc/scrape.php';
include 'inc/database.php';

if (!isset($_GET['game']) || !isset($_GET['gid'])) {
	header('HTTP/1.0 400 Bad Request');
	die('{"status": "error","code": 400,"message" : "Missing or incorrect syntax"}');
}

$game = $_GET['game'];
$gid = (int)$_GET['gid'];
$i = 1;

$url = sprintf('http://xwis.net/%s/games/%d/', $game, $gid);
$html = substr(scrape($url), 1074);

// extract players
preg_match_all('/<img .*? title="(.*?)".*? title="(.*?)p">([\w\d]{3,9})<\/(?:a|span)><td>(.*?)(W|L|DC|RE)<td class=ar>(.*?)</', $html, $p);

// extract game information
preg_match_all('/title=([\d]+)>([\d]+)<\/a>.*?class=ar>([\d:]+)<td>([\w\d\s~\.-]+)<td class=ar>(.*?)<td class=ar>([\d]+)<td class=ar>(.*?)<td class=ar>(.*?)<td class=ar>(.*?)</', $html, $g);

// extract player stats
preg_match_all('/<th class=ar>([\w\d]{3,9})<th>killed.*?units<td class=ar>(.*?)<td class=ar>(.*?)<td class=ar>(.*?)<tr>/', $html, $units);
preg_match_all('/<th class=ar>([\w\d]{3,9})<th>killed.*?buildings<td class=ar>(.*?)<td class=ar>(.*?)<td class=ar>(.*?)<td class=ar>(.*?)<tr>/', $html, $buildings);
preg_match_all('/<th class=ar>([\w\d]{3,9})<th>killed.*?infantry<td class=ar>(.*?)<td class=ar>(.*?)<td class=ar>(.*?)<tr>/', $html, $infantry);
preg_match_all('/<th class=ar>([\w\d]{3,9})<th>killed.*?planes<td class=ar>(.*?)<td class=ar>(.*?)<td class=ar>(.*?)</', $html, $planes);

// yes, i know im overriding $game.
$game = new stdClass();
$game->gid = (int)$_GET['gid'];
$game->gameId = (int)$g[1][0];
$game->duration = $g[3][0];
$game->dura = duration($g[3][0]);
$game->game = $_GET['game'];
$game->map = $g[4][0];
$game->timestamp = strtotime($g[5][0]);
$game->fps = (int)$g[6][0];
$game->crates = (!empty($g[7][0]) ? 'true' : 'false');
$game->syncfail = (!empty($g[8][0]) ? 'true' : 'false');
$game->tourney = (!empty($g[9][0]) ? ($g[9][0] == 'C' ? 'clan' : 'player') : 'false');

$game->players = new stdClass();
$game->teams = new stdClass();
foreach($p[3] as $k => $player) {
	$ps = new stdClass();
	$ps->name = $player;
	$ps->country = $p[1][$k];
	$ps->resolution = $p[5][$k];
	$ps->points_exchanged =  (int)trim($p[6][$k], '+-');

	$rank = explode(' ', $p[2][$k]);
	$ps->rank = (int)trim($rank[0], '#');
	$ps->wins = (int)$rank[1];
	$ps->losses = (int)$rank[3];
	$ps->points =(int)$rank[4];

	$ps->clan = new stdClass();
	$ps->clan = 'none';
	$team = 'team'. ($i % 2 ? 1 : 2);
	if (!empty($p[4][$k])) {
		preg_match_all('/title="(.*?)p">(.*?)</', $p[4][$k], $c);
		$ps->clan = $team = $c[2][0];

		if (!isset($game->teams->{$team}))  {
			$cl = new stdClass();
			$rank = explode(' ', $c[1][0]);
			$cl->rank = (int)trim($rank[0], '#');
			$cl->wins = (int)$rank[1];
			$cl->losses = (int)$rank[3];
			$cl->points = (int)trim($rank[4], '+-');

			$game->teams->{$team}  = $cl;
		}
	}
	$game->teams->{$team}->players[] = $player;
	
	// game stats aren't in same order as playerlist. thanks Olaf.
	$key = array_search($player, $units[1]);
	$ps->stats = new stdClass();
	$ps->stats->units = new stdClass();
	$ps->stats->units->killed = (int)$units[2][$key];
	$ps->stats->units->bought = (int)$units[3][$key];
	$ps->stats->units->left = (int)$units[4][$key];

	$ps->stats->buildings = new stdClass();
	$ps->stats->buildings->killed = (int)$buildings[2][$key];
	$ps->stats->buildings->bought = (int)$buildings[3][$key];
	$ps->stats->buildings->left = (int)$buildings[4][$key];
	$ps->stats->buildings->captured = (int)$buildings[5][$key];

	$ps->stats->infantry = new stdClass();
	$ps->stats->infantry->killed = (int)$infantry[2][$key];
	$ps->stats->infantry->bought = (int)$infantry[3][$key];
	$ps->stats->infantry->left = (int)$infantry[4][$key];

	$ps->stats->planes = new stdClass();
	$ps->stats->planes->killed = (int)$planes[2][$key];
	$ps->stats->planes->bought = (int)$planes[3][$key];
	$ps->stats->planes->left = (int)$planes[4][$key];

	$game->players->{$player} = $ps;
	$i++;
}

// player colors
preg_match_all('/color: (#[\w]{3,6})">&nbsp;<th class=ar>(\w{3,9})/', $html, $colors);
if (!empty($colors) && count($colors) == 3) {
	foreach($colors[2] as $k => $n) {
		$col = 0;
		switch($colors[1][$k]) {
			case '#dee308': $col = 1; break;
			case '#ff1818': $col = 2; break;
			case '#2975e7': $col = 3; break;
			case '#39d329': $col = 4; break;
			case '#ffa218': $col = 5; break;
			case '#31d7e7': $col = 6; break;
			case '#9428bd': $col = 7; break;
			case '#ff9aef': $col = 8; break;
		}
		$game->players->{$n}->color = $col;
	}
}

// screenshots
preg_match_all('/href="\/(ra2|ts|yr)\/screenshots\/([\d]+)/', $html, $s);

if (!empty($s)) {
	$game->screenshots = array();
	foreach($s[1] as $k => $a) {
		$u = sprintf('http://xwis.net/%s/screenshots/%d/', $a, $s[2][$k]);
		$game->screenshots[] = array('image' => $u, 'thumbnail' => $u .'thumb/', 'id' => (int)$s[2][$k]);
	}
}

function duration($str_time) {
	$str_time = preg_replace("/^([\d]{1,2})\:([\d]{2})$/", "00:$1:$2", $str_time);
	sscanf($str_time, "%d:%d:%d", $hours, $minutes, $seconds);
	return $hours * 3600 + $minutes * 60 + $seconds;
}

die(_json_encode($game));

//printf('<pre>%s</pre>', print_r($p, 1));
//printf('<pre>%s</pre>', print_r($g, 1));
//printf('<pre>%s</pre>', print_r($s, 1));
//printf('<pre>%s</pre>', print_r($game, 1));