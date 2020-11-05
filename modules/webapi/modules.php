<?php 

$meta = new lrg_metadata;
$endpoints = [];

if (!empty($report)) {
  include_once(__DIR__ . "/../../modules/view/__post_load.php");

  if(empty($mod)) $mod = "";

  include_once(__DIR__ . "/modules/info.php");
  include_once(__DIR__ . "/modules/overview.php");
  include_once(__DIR__ . "/modules/records.php");
  include_once(__DIR__ . "/modules/haverages.php");
  include_once(__DIR__ . "/modules/participants.php");
  include_once(__DIR__ . "/modules/matches.php");
  include_once(__DIR__ . "/modules/combos.php");
  include_once(__DIR__ . "/modules/meta_graph.php");
  include_once(__DIR__ . "/modules/party_graph.php");
  include_once(__DIR__ . "/modules/pickban.php");
  include_once(__DIR__ . "/modules/draft.php");
  include_once(__DIR__ . "/modules/vsdraft.php");
  include_once(__DIR__ . "/modules/positions.php");
  include_once(__DIR__ . "/modules/positions_matches.php");
  include_once(__DIR__ . "/modules/pvp.php");
  include_once(__DIR__ . "/modules/hvh.php");
  include_once(__DIR__ . "/modules/summary.php");
  include_once(__DIR__ . "/modules/matchcards.php");
  include_once(__DIR__ . "/modules/teams_raw.php");
  include_once(__DIR__ . "/modules/teams.php");
  include_once(__DIR__ . "/modules/roster.php");
  include_once(__DIR__ . "/modules/laning.php");
  include_once(__DIR__ . "/modules/counters.php");

  $endpoints['__fallback'] = function() use (&$endpoints) {
    return $endpoints['info'];
  };
} else {
  include_once(__DIR__ . "/modules/list.php");
  include_once(__DIR__ . "/modules/metadata.php");
  include_once(__DIR__ . "/modules/locales.php");
  include_once(__DIR__ . "/modules/getcache.php");
  include_once(__DIR__ . "/modules/raw.php");

  $endpoints['__fallback'] = function() use (&$endpoints) {
    return $endpoints['list'];
  };
}

$mod = str_replace("/", "-", $mod);
$modline = array_reverse(explode("-", $mod));
$vars = [];

foreach ($modline as $ml) {
  if (!isset($endp_name) && isset($endpoints[$ml])) {
    $endp_name = $ml;
  }
  if (strpos($ml, "region") !== FALSE && $ml != "regions") $vars['region'] = (int)str_replace("region", "", $ml);
  if (strpos($ml, "position_") !== FALSE) $vars['position'] = str_replace("position_", "", $ml);
  if (strpos($ml, "heroid") !== FALSE) $vars['heroid'] = (int)str_replace("heroid", "", $ml);
  if (strpos($ml, "playerid") !== FALSE) $vars['playerid'] = (int)str_replace("playerid", "", $ml);
  if (strpos($ml, "team") !== FALSE && $ml != "teams") $vars['team'] = (int)str_replace("team", "", $ml);
  if (strpos($ml, "teamid") !== FALSE) $vars['team'] = (int)str_replace("teamid", "", $ml);
  //if (isset($vars['team'])) $vars['teamid'] = $vars['team']; 
}
if (isset($_GET['gets'])) $vars['gets'] = explode(",", strtolower($_GET['gets']));
if (isset($_GET['rep'])) $vars['rep'] = strtolower($_GET['rep']);
if(isset($_GET['cat']) && !empty($_GET['cat'])) $vars['cat'] = $_GET['cat'];

if (empty($endp_name)) {
  $endp = $endpoints['__fallback']();
  $endp_name = array_search($endp, $endpoints);
} else $endp = $endpoints[$endp_name];
try {
  $result = $endp($modline, $vars, $report);
} catch (\Throwable $e) {
  if (!isset($resp['errors'])) $resp['errors'] = [];
    $resp['errors'][] = $e->getMessage();
}
