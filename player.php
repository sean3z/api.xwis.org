<?php
header('Content-type: application/json');
header('Access-Control-Allow-Origin: *');
header('Expires: '. gmdate('D, d M Y H:i:s \G\M\T', time() + 120));

include 'inc/apiauth.php';
include 'inc/scrape.php';
include 'inc/database.php';
include 'inc/factions.php';

if (!isset($_GET['game']) || !isset($_GET['player'])) {
	header('HTTP/1.0 400 Bad Request');
	die('{"status": "error","code": 400,"message" : "Missing or incorrect syntax"}');
}

$game = $_GET['game'];
$player = new stdClass();
$player->game = $game;
$player->name = substr(preg_replace('/[^a-zA-Z0-9]+/', '', strtolower($_GET['player'])), 0, 9);

$url = sprintf('http://xwis.net/%s/pl/%s/', $game, $player->name);
$buffer = scrape($url);

 preg_match_all('/<hr><div style="float: right;">(.*?)<\/div>/ism', $buffer, $match);
  //printf('<pre><div style="display:none;">%s</div></pre>', print_r($match, 1));

  preg_match_all('/[^<div style="float: right;">](.*?)<br>/', $match[0][0], $stats);
  //printf('<pre><div style="display:none;">%s</div></pre>', print_r($stats, 1));

  foreach($stats[0] as $key => $value) {
    $value = strip_tags($value);
    if (substr($value, -3) == 'FPS') $player->fps = (int)substr($value, 0, -4);
    if ($value[0] == '#') {
        $x = explode(' ', $value);
        $player->ladder = (int)substr($x[0], 1);
        $player->wins = (int)$x[1];
        $player->losses = (int)$x[3];
        $player->points = (int)substr($x[4], 0, -1);
    }
    if (substr($value, -11) == 'disconnects') $player->discon = (int)substr($value, 0, -12);
    if (substr($value, -6) == 'errors') $player->recon = (int)substr($value, 0, -20);
    if (preg_match('/days|hours|minutes/', $value)) $player->time = explode(' ', $value);
  }

  //$player->url = 'http://xwis'. ((isset($_GET['site']) ? '.net' : '.us')) .'/'. $player->game .'/pl/'. $player->name .'/';

  preg_match_all('/([\d]{1,})x<td class=ac><img src=".*?" alt="([\w\d\s]{1,})"[^>]+>/', $match[1][0], $stats);

  foreach($stats[2] as $key => $name) $player->countries[$name] = (int)$stats[1][$key];

  //printf('<pre><div style="display:none;">%s</div></pre>', print_r($match[1][0], 1));

  preg_match_all('/([\d]{1,})x<td>([\w\d\s]{1,})/', $match[1][0], $stats);

  foreach($stats[2] as $key => $name) $player->maps[$name] = (int)$stats[1][$key];

  preg_match_all('/<a *[^>]+>([\d]{1,})<\/a>.*? alt="([\w\d\s]{1,})" .*?<td>([W|L])<td class=ar>([-|+][\d]{1,}).*? alt="([\w\d\s]{1,})" .*?<a *[^>]+>([\w\d]{1,})<\/a>.*?<td class=ar>([-|+][\d]{1,}).*?([\d:]{2,})<td>([\w\d\s]{1,})<td class=ar>(.*?)</', $buffer, $games);
  //printf('<pre>%s</pre><br /><br />', print_r($games, 1));

  foreach($games[1] as $key => $gid) {
    $player->games[] = array(
      'gid' => (int)$gid,
      'player_country' => $games[2][$key],
      'resolution' => $games[3][$key],
      'player_points' => (int)trim($games[4][$key], '-+'),
      'opponent_country' => $games[5][$key],
      'opponent' => $games[6][$key],
      'opponent_points' => (int)trim($games[7][$key], '-+'),
      'duration' => $games[8][$key],
      'map' => $games[9][$key],
      'timestamp' => strtotime($games[10][$key]),
    );
  }

die(_json_encode($player));