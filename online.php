<?php
header('Content-type: application/json');
header('Access-Control-Allow-Origin: *');

class XWIS {
  public $url;

  private function url_param($game) {
    $this->url = sprintf('http://xwis.net/%s/online/?pure=', $game);
  }

  private function retrieve() {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $this->url);
    curl_setopt($ch, CURLOPT_HEADER, 0); 
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
    curl_setopt($ch, CURLOPT_ENCODING, '');
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.7.5) Gecko/20041107 Firefox/1.0'); 
    $buffer = curl_exec($ch);
    curl_close($ch);
    if (empty($buffer)) die(header('HTTP/1.0 504 Gateway Time-out'));
    return $buffer;
  }

  public function online($game) {
    $yr = 0;
    $this->url_param($game);
    foreach(explode("\n", $this->retrieve()) as $key => $value) {
      if (empty($value)) continue;
      $value = explode("\t", $value);
      if ($value[2] == 'RA2 YR') { $yr++; if ($game == 'ra2') continue; }
      $online['players'][strtolower($value[0])] = array('nick' => $value[0], 'locale' => ( (!empty($value[1])) ? $value[1] : 'In Game' ), 'game' => $value[2], 'clan' => $value[3]);
    }
    if ($yr > 0) $online['##yuri##'] = $yr;
    $online['total'] = count($online['players']);
    if ($_GET['mode'] == 'totals') unset($online['players']);
    return $online;
  }
}

$games = array('ra2', 'ts', 'rg');
if(!isset($_GET['game'])) {
  $return['ra2']['total'] = 0;
  $return['yr']['total'] = 0;
  $return['ts']['total'] = 0;
  $return['rg']['total'] = 0;
}
if (isset($_GET['game'])) $games = array($_GET['game']);
$xwis = new XWIS();
foreach($games as $game) $return[$game] = $xwis->online($game);

foreach($return as $game => $data) {
  if (isset($return[$game]['##yuri##'])) {
    $return['yr']['total'] = $return[$game]['##yuri##'];
    unset($return[$game]['##yuri##']);
  }
}

//include 'mysql.php';

if ($_GET['mode'] == 'player' && isset($_GET['player'])) {
  if ( !isset($_GET['game']) || !isset($return[$_GET['game']]) ) die('no bueno');
  $player = new stdClass();
  $player->nick = strtolower($_GET['player']);
  $player->online = 0;
  $player->game = strtolower($_GET['game']);
  $player->lastseen = 0;
  if ( isset($return[$player->game]['players'][$player->nick]) ) {
    $player->online = 1;
    $player->locale = $return[$player->game]['players'][$player->nick]['locale'];
  }
  $data = addslashes(serialize($player));
  //@$mysqli->query('INSERT INTO `dump` (`service`, `timestamp`, `data`) VALUES ("online", '. time() .',"'. $data.'")');
  //@$mysqli->close();
  die(json_encode($player));
}

$data = addslashes(serialize($return));
//@$mysqli->query('INSERT INTO `dump` (`service`, `timestamp`, `data`) VALUES ("online", '. time() .'"'. $data.'")');
//@$mysqli->close();
die( json_encode($return) );
?>