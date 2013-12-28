<?php

$mode = 'json';

if (isset($_GET['mode']) && $_GET['mode'] == 'xml') {
  include 'xml_encode.php';
  header('Content-type: application/xml');
  $mode = 'xml';
} else {
  header('Content-type: application/json');
  header('Access-Control-Allow-Origin: *');
}

if (!isset($_GET['wins']) || !isset($_GET['losses'])) die(preg_replace('/[\r\n\t]+/', ' ', file_get_contents('resources/rank.json')));

// why i've yet to put these ranks in a database, is beyond me....

function getRank($xp) {
  if ($xp <= 7999) {
    $rank['name'] = 'Private First Class';
    $rank['rank'] = 1;
    $rank['next'] = 8000;
    $rank['begin'] = 1;
  }
  else if ($xp >= 8000 && $xp <= 17999) {
    $rank['name'] = 'Private First Class 1 Star';
    $rank['rank'] = 2;
    $rank['next'] = 18000;
    $rank['begin'] = 8000;
  }
  else if ($xp >= 18000 && $xp <= 28999) {
    $rank['name'] = 'Private First Class 2 Stars';
    $rank['rank'] = 3;
    $rank['next'] = 29000;
    $rank['begin'] = 18000;
  }
  else if ($xp >= 29000 && $xp <= 40999) {
    $rank['name'] = 'Private First Class 3 Stars';
    $rank['rank'] = 4;
    $rank['next'] = 41000;
    $rank['begin'] = 29000;
  }
  else if ($xp >= 41000 && $xp <= 53999) {
    $rank['name'] = 'Lance Corporal';
    $rank['rank'] = 5;
    $rank['next'] = 54000;
    $rank['begin'] = 41000;
  }
  else if ($xp >= 54000 && $xp <= 66999) {
    $rank['name'] = 'Lance Corporal 1 Star';
    $rank['rank'] = 6;
    $rank['next'] = 67000;
    $rank['begin'] = 54000;
  }
  else if ($xp >= 67000 && $xp <= 80999) {
    $rank['name'] = 'Lance Corporal 2 Stars';
    $rank['rank'] = 7;
    $rank['next'] = 96000;
    $rank['begin'] = 67000;
  }
  else if ($xp >= 81000 && $xp <= 95999) {
    $rank['name'] = 'Lance Corporal 3 Stars';
    $rank['rank'] = 8;
    $rank['next'] = 96000;
    $rank['begin'] = 81000;
  }
  else if ($xp >= 96000 && $xp <= 110999) {
    $rank['name'] = 'Corporal';
    $rank['rank'] = 9;
    $rank['next'] = 111000;
    $rank['begin'] = 96000;
  }
  else if ($xp >= 111000 && $xp <= 129999) {
    $rank['name'] = 'Corporal 1 Star';
    $rank['rank'] = 10;
    $rank['next'] = 130000;
    $rank['begin'] = 111000;
  }
  else if ($xp >= 130000 && $xp <= 149999) {
    $rank['name'] = 'Corporal 2 Stars';
    $rank['rank'] = 11;
    $rank['next'] = 150000;
    $rank['begin'] = 130000;
  }
  else if ($xp >= 150000 && $xp <= 169999) {
    $rank['name'] = 'Corporal 3 Stars';
    $rank['rank'] = 12;
    $rank['next'] = 170000;
    $rank['begin'] = 150000;
  }
  else if ($xp >= 170000 && $xp <= 189999) {
    $rank['name'] = 'Sergeant';
    $rank['rank'] = 13;
    $rank['next'] = 190000;
    $rank['begin'] = 170000;
  }
  else if ($xp >= 190000 && $xp <= 219999) {
    $rank['name'] = 'Sergeant 1 Star';
    $rank['rank'] = 14;
    $rank['next'] = 220000;
    $rank['begin'] = 190000;
  }
  else if ($xp >= 220000 && $xp <= 249999) {
    $rank['name'] = 'Sergeant 2 Stars';
    $rank['rank'] = 15;
    $rank['next'] = 250000;
    $rank['begin'] = 220000;
  }
  else if ($xp >= 250000 && $xp <= 279999) {
    $rank['name'] = 'Sergeant 3 Stars';
    $rank['rank'] = 16;
    $rank['next'] = 280000;
    $rank['begin'] = 250000;
  }
  else if ($xp >= 280000 && $xp <= 309999) {
    $rank['name'] = 'Staff Sergeant';
    $rank['rank'] = 17;
    $rank['next'] = 310000;
    $rank['begin'] = 280000;
  }
  else if ($xp >= 310000 && $xp <= 339999) {
    $rank['name'] = 'Staff Sergeant 1 Star';
    $rank['rank'] = 18;
    $rank['next'] = 340000;
    $rank['begin'] = 310000;
  }
  else if ($xp >= 340000 && $xp <= 369999) {
    $rank['name'] = 'Staff Sergeant 2 Stars';
    $rank['rank'] = 19;
    $rank['next'] = 370000;
    $rank['begin'] = 340000;
  }
  else if ($xp >= 370000 && $xp <= 399999) {
    $rank['name'] = 'Gunnery Sergeant';
    $rank['rank'] = 20;
    $rank['next'] = 400000;
    $rank['begin'] = 370000;
  }
  else if ($xp >= 400000 && $xp <= 429999) {
    $rank['name'] = 'Gunnery Sergeant 1 Star';
    $rank['rank'] = 21;
    $rank['next'] = 430000;
    $rank['begin'] = 400000;
  }
  else if ($xp >= 430000 && $xp <= 469999) {
    $rank['name'] = 'Gunnery Sergeant 2 Star';
    $rank['rank'] = 22;
    $rank['next'] = 470000;
    $rank['begin'] = 430000;
  }
  else if ($xp >= 470000 && $xp <= 509999) {
    $rank['name'] = 'Master Sergeant';
    $rank['rank'] = 23;
    $rank['next'] = 510000;
    $rank['begin'] = 470000;
  }
  else if ($xp >= 510000 && $xp <= 549999) {
    $rank['name'] = 'Master Sergeant 1 Star';
    $rank['rank'] = 24;
    $rank['next'] = 550000;
    $rank['begin'] = 510000;
  }
  else if ($xp >= 550000 && $xp <= 589999) {
    $rank['name'] = 'Master Sergeant 2 Stars';
    $rank['rank'] = 25;
    $rank['next'] = 590000;
    $rank['begin'] = 550000;
  }
  else if ($xp >= 590000 && $xp <= 629999) {
    $rank['name'] = 'First Sergeant';
    $rank['rank'] = 26;
    $rank['next'] = 630000;
    $rank['begin'] = 590000;
  }
  else if ($xp >= 630000 && $xp <= 669999) {
    $rank['name'] = 'First Sergeant 1 Star';
    $rank['rank'] = 27;
    $rank['next'] = 670000;
    $rank['begin'] = 630000;
  }
  else if ($xp >= 670000 && $xp <= 709999) {
    $rank['name'] = 'First Sergeant 2 Stars';
    $rank['rank'] = 28;
    $rank['next'] = 710000;
    $rank['begin'] = 670000;
  }
  else if ($xp >= 710000 && $xp <= 759999) {
    $rank['name'] = 'Master Gunnery Sergeant';
    $rank['rank'] = 29;
    $rank['next'] = 760000;
    $rank['begin'] = 710000;
  }
  else if ($xp >= 760000 && $xp <= 809999) {
    $rank['name'] = 'Master Gunnery Sergeant 1 Star';
    $rank['rank'] = 30;
    $rank['next'] = 810000;
    $rank['begin'] = 760000;
  }
  else if ($xp >= 810000 && $xp <= 859999) {
    $rank['name'] = 'Master Gunnery Sergeant 2 Stars';
    $rank['rank'] = 31;
    $rank['next'] = 860000;
    $rank['begin'] = 810000;
  }
  else if ($xp >= 860000 && $xp <= 909999) {
    $rank['name'] = 'Sergeant Major';
    $rank['rank'] = 32;
    $rank['next'] = 910000;
    $rank['begin'] = 860000;
  }
  else if ($xp >= 910000 && $xp <= 959999) {
    $rank['name'] = 'Sergeant Major 1 Star';
    $rank['rank'] = 33;
    $rank['next'] = 960000;
    $rank['begin'] = 910000;
  }
  else if ($xp >= 960000 && $xp <= 1009999) {
    $rank['name'] = 'Sergeant Major 2 Star';
    $rank['rank'] = 34;
    $rank['next'] = 1010000;
    $rank['begin'] = 960000;
  }
  else if ($xp >= 1010000 && $xp <= 1059999) {
    $rank['name'] = 'Warrant Officer One';
    $rank['rank'] = 35;
    $rank['next'] = 1060000;
    $rank['begin'] = 1010000;
  }
  else if ($xp >= 1060000 && $xp <= 1109999) {
    $rank['name'] = 'Chief Warrant Officer Two';
    $rank['rank'] = 36;
    $rank['next'] = 1110000;
    $rank['begin'] = 1060000;
  }
  else if ($xp >= 1110000 && $xp <= 1164999) {
    $rank['name'] = 'Chief Warrant Officer Three';
    $rank['rank'] = 37;
    $rank['next'] = 1165000;
    $rank['begin'] = 1110000;
  }
  else if ($xp >= 1165000 && $xp <= 1219999) {
    $rank['name'] = 'Chief Warrant Officer Four';
    $rank['rank'] = 38;
    $rank['next'] = 1220000;
    $rank['begin'] = 1165000;
  }
  else if ($xp >= 1220000 && $xp <= 1279999) {
    $rank['name'] = 'Chief Warrant Officer Five';
    $rank['rank'] = 39;
    $rank['next'] = 1280000;
    $rank['begin'] = 1220000;
  }
  else if ($xp >= 1280000 && $xp <= 1339000) {
    $rank['name'] = 'Second Lieutenant';
    $rank['rank'] = 40;
    $rank['next'] = 1340000;
    $rank['begin'] = 1280000;
  }
  else if ($xp >= 1340000 && $xp <= 1399999) {
    $rank['name'] = 'First Lieutenant';
    $rank['rank'] = 41;
    $rank['next'] = 1400000;
    $rank['begin'] = 1340000;
  }
  else if ($xp >= 1400000 && $xp <= 1459999) {
    $rank['name'] = 'Captain';
    $rank['rank'] = 42;
    $rank['next'] = 1460000;
    $rank['begin'] = 1400000;
  }
  else if ($xp >= 1460000 && $xp <= 1519999) {
    $rank['name'] = 'Major';
    $rank['rank'] = 43;
    $rank['next'] = 1520000;
    $rank['begin'] = 1460000;
  }
  else if ($xp >= 1520000 && $xp <= 1599999) {
    $rank['name'] = 'Lt. Colonel';
    $rank['rank'] = 44;
    $rank['next'] = 1600000;
    $rank['begin'] = 1520000;
  }
  // else if ($xp >= 1600000 && $xp <= 1999999) {
  //   $rank['name'] = 'Colonel';
  //   $rank['rank'] = 45;
  //   $rank['next'] = 2000000;
  //   $rank['begin'] = 1600000;
  // } 
  else if ($xp >= 1600000) {
    $rank['name'] = 'Colonel';
    $rank['rank'] = 45;
    //$rank['next'] = 2000000;
    $rank['begin'] = 1600000;
  }
  return $rank;
}

function experience($player) {
  $recon = ( ( isset($player->recon) ) ? $player->recon: 0);
  return round(($player->wins * ($player->wins + $player->losses) * 19.7) - ($recon * 7.8));
}

$player = new stdClass();
$player->wins = (is_numeric($_GET['wins']) ? $_GET['wins'] : 0);
$player->losses = (is_numeric($_GET['losses']) ? $_GET['losses'] : 0);
$player->recon = 0;
$player->discon = 0;

if (isset($_GET['recon'])) $player->recon = (is_numeric($_GET['recon']) ? $_GET['recon'] : 0);
if (isset($_GET['discon'])) $player->discon = (is_numeric($_GET['discon']) ? $_GET['discon'] : 0);

$player->experience = experience($player);
if ($player->experience > 1600000) $player->experience = 1600000;
$player->rank = getRank($player->experience);
if (isset($player->rank['next'])) $player->rank['needed'] = ($player->rank['next'] - $player->experience);
$player->progress_percent = round(($player->experience / $player->rank['next']) * 100, 2);


if ($mode == 'xml') {
  die(xml_encode((array)$player));
} else {
  die(json_encode($player));
}

?>