<?php

function rg_generator_pvp_unwrap_data(&$context, &$context_wrs, $heroes_flag = true) {
  if(!sizeof($context)) return [];

  $pvp = [];
  $id = $heroes_flag ? "heroid" : "playerid";
  $sid = $heroes_flag ? "h" : "p";

  if (is_wrapped($context)) {
    $context = unwrap_data($context);
  }

  if(empty($context_wrs)) $nodiff = true;
  else {
    $nodiff = false;
    $wr_id = $heroes_flag ? "winrate_picked" : "winrate";
  }

  foreach($context as $line) {
    if( !isset($pvp[ $line[$id.'1'] ]) )
      $pvp[ $line[$id.'1'] ] = [];
    if( !isset($pvp[ $line[$id.'2'] ]) )
      $pvp[ $line[$id.'2'] ] = [];

    $pvp[ $line[$id.'1'] ][ $line[$id.'2'] ] = [
      "winrate" => round($line[$sid.'1winrate'], 5),
      "matches" => $line['matches'],
      "won" => $line[$sid.'1won'],
      "lost" => $line['matches']-$line[$sid.'1won']
    ];
    if(!$nodiff) $pvp[ $line[$id.'1'] ][ $line[$id.'2'] ]['diff'] = round($line[$sid.'1winrate']-$context_wrs[$line[$id.'1']][$wr_id], 5);

    $pvp[ $line[$id.'2'] ][ $line[$id.'1'] ] = [
      "winrate" => round(1-$line[$sid.'1winrate'], 5),
      "matches" => $line['matches'],
      "won" => $line['matches']-$line[$sid.'1won'],
      "lost" => $line[$sid.'1won']
    ];
    if(!$nodiff) $pvp[ $line[$id.'2'] ][ $line[$id.'1'] ]['diff'] = round(1-$line[$sid.'1winrate']-$context_wrs[$line[$id.'2']][$wr_id], 5);

    if(isset($line['exp'])) {
      $pvp[ $line[$id.'2'] ][ $line[$id.'1'] ]['expectation'] = $line['exp'];

      $pvp[ $line[$id.'1'] ][ $line[$id.'2'] ]['expectation'] = $line['exp'];
    }
  }

  return $pvp;
}

?>
