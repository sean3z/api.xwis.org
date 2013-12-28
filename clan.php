<?php
header('Content-type: application/json');
header('Access-Control-Allow-Origin: *');

include 'inc/apiauth.php';
include 'inc/scrape.php';
include 'inc/database.php';

if (!isset($_GET['game']) || !isset($_GET['clan'])) {
  header('HTTP/1.0 400 Bad Request');
  die('{"status": "error","code": 400,"message" : "Missing or incorrect syntax"}');
}

$game = $_GET['game'];
$name = $_GET['clan'];
$i = 1;

$url = sprintf('http://xwis.net/%s/clans/%s/', $game, $name);
$html = scrape($url);

// founder info - some clans missing this
preg_match_all('/Founder<td>.*?([\w\d]{3,9})</', $html, $f);

// created (founded) info
preg_match_all('/Founded<td>([\w\d\s:-]+)/', $html, $c);

// rank info
preg_match_all('/(Current|Previous)<td>.*?#([\d\s\/]+)p/', $html, $r);

// members
preg_match_all('/players\/([\w\d]{3,9})\//', $html, $p);

// awards
preg_match_all('/#([\d]+) (RA2|TS|RA2 YR), ([\w\d\s]+)/', $html, $a);

$clan = new stdClass();
$clan->founder = null;
if (!empty($f[1])) $clan->founder = $f[1][0];

$clan->created = strtotime($c[1][0]);

$clan->game = $game;
$clan->name = $name;
$clan->abbreviation = $name;

$clan->rank = new stdClass();
$clan->rank->current = null;
$clan->rank->previous = null;

if (!empty($r[1])) {
  foreach($r[1] as $k => $n) {
    $j = explode(' ', $r[2][$k]);

    $h = new stdClass();
    $h->rank = $j[0];
    $h->wins = $j[1];
    $h->losses = $j[3];
    $h->points = $j[4];

    $clan->rank->{strtolower($n)} = $h;
  }
}

$clan->members = array();
foreach($p[1] as $n) $clan->members[] = $n;
// remove founder, since it can get added twice due two links. sometimes, clan founder isn't linked
if (count($clan->members) > 1 && $clan->members[0] == $clan->members[1]) array_shift($clan->members);

$clan->awards = array();
if (!empty($a[1])) {
  foreach($a[1] as $k => $n) {
  	$g = (($a[2][$k] == 'RA2 YR') ? 'yr' : strtolower($a[2][$k]));
  	$clan->awards[$g][] = '#'. $n .' '. $a[3][$k];
  }
}


// recent games - don't fetch if no current rank. (means they don't have any recent games)
$html = '';

//printf('<pre>%s</pre>', print_r($f, 1));
//printf('<pre>%s</pre>', print_r($c, 1));
//printf('<pre>%s</pre>', print_r($r, 1));
//printf('<pre>%s</pre>', print_r($a, 1));
//printf('<pre>%s</pre>', print_r($p, 1));
//printf('<pre>%s</pre>', print_r($clan, 1));

die(_json_encode($clan));
?>