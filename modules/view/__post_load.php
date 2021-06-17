<?php
function generate_positions_strings() {
  global $strings;

  for ($i=1; $i>=0; $i--) {
    $strings['en']["position_$i.0"] = ($i ? locale_string("core") : locale_string("support"));
    for ($j=1; $j<6 && $j>0; $j++) {
      //if (!$i) { $j = 0; }
      if(!isset($strings['en']["position_$i.$j"]))
        $strings['en']["position_$i.$j"] = ($i ? locale_string("core") : locale_string("support"))." ".locale_string("lane_$j");

      //if (!$i) { break; }
    }
  }
}

if ((stripos($mod, "positions") !== FALSE || stripos($mod, "role") !== FALSE) && isset($strings['en'])) {
  generate_positions_strings();
}

# legacy name for Radiant Winrate
if (compare_ver($report['ana_version'], array(1,1,1,-4,0)) < 0) {
    $strings[$locale]['rad_wr'] = $strings[$locale]['radiant_wr'];
}

if(isset($report['versions'])) {
  foreach($report['versions'] as $k => $v) {
    $mode = (int)($k/100);
    if(!isset($meta->versions[$mode])) {
        for($i = $mode; $i > 0; $i--) {
            if(isset($meta['versions'][$i])) {
                break;
            }
        }
        $diff = $mode - $i;
        $parent_patch = explode(".", $meta->versions[$i]);
        $parent_patch[1] = (int)$parent_patch[1] + $diff;
        if ($parent_patch[1] < 10)
            $parent_patch[1] = "0".$parent_patch[1];
        $meta->versions[$mode] = implode(".", $parent_patch);

        unset($diff);
        unset($parent_patch);
    }
  }
}

if (isset($report['provider_override'])) {
  if (isset($report['provider_override']['_icons'])) {
    $icons_provider = $report['provider_override']['_icons'];
    unset($report['provider_override']['_icons']);
  }
  if (isset($report['provider_override']['_portraits'])) {
    $portraits_provider = $report['provider_override']['_portraits'];
    unset($report['provider_override']['_portraits']);
  }
  $links_providers = $report['provider_override'];
}

if (isset($report['localized']) && isset($report['localized'][$locale])) {
  $report['league_name'] = $report['localized'][$locale]['name'] ?? $report['league_name'];
  $report['league_desc'] = $report['localized'][$locale]['desc'] ?? $report['league_desc'];
}

if (!empty($report['teams']) && !empty($report['matches']) && !empty($report['match_participants_teams'])) {
  $partCnts = [];
  $meetCnts = [];
  $report['match_parts_strings'] = [];
  $mids = array_keys($report['matches']);
  sort($mids);

  foreach ($mids as $mid) {
    $teams = [ $report['match_participants_teams'][$mid]['radiant'] ?? 0, $report['match_participants_teams'][$mid]['dire'] ?? 0 ];
    $time = $report['matches_additional'][$mid]['date'];
    $duration = $report['matches_additional'][$mid]['duration'];
    if ($teams[0] && $teams[1])
      $teamsStr = team_tag( min($teams) ).' '.locale_string('versus').' '.team_tag( max($teams) );
    else {
      $teamsStr = ( $teams[0] ? team_tag($teams[0]) : locale_string('radiant') ).' '.locale_string('versus').' '.( $teams[1] ? team_tag($teams[1]) : locale_string('dire') );
    }

    if (!isset($meetCnts[$teamsStr])) {
      $meetCnts[$teamsStr] = [
        0,
        null,
        0
      ];
    }
    // 3600 * 4 = 10800
    $timeDiff = $meetCnts[$teamsStr][1] ? $time - $meetCnts[$teamsStr][1] - $duration : 0;
    if (!$meetCnts[$teamsStr][1] || ($partCnts[$teamsStr] < 2 && $timeDiff > 14400) || ($partCnts[$teamsStr] >= 2 && $timeDiff > $meetCnts[$teamsStr][2] * 2)) {
      $meetCnts[$teamsStr][0]++;
      $partCnts[$teamsStr] = 0;
    }
    $meetCnts[$teamsStr][1] = $time;
    $meetCnts[$teamsStr][2] = $timeDiff;

    if (!isset($partCnts[$teamsStr])) $partCnts[$teamsStr] = 0;
    $partCnts[$teamsStr]++;
    $cnt = $partCnts[$teamsStr];
    
    $report['match_parts_strings'][$mid] = $teamsStr
      .' - '.locale_string('meet_num').' '.$meetCnts[$teamsStr][0]
      .' - '.locale_string('game_num').' '.$cnt;
    $report['match_parts_series_num'][$mid] = $meetCnts[$teamsStr][0];
    $report['match_parts_game_num'][$mid] = $cnt;
  }
}

if (!empty($report['settings']['heroes_snapshot'])) {
  $meta['heroes'];
  $diff = array_diff(array_keys($meta['heroes']), $report['settings']['heroes_snapshot']);
  foreach ($diff as $hid) {
    unset($meta['heroes'][$hid]);
  }
} else if (!empty($report['settings']['heroes_exclude'])) {
  $meta['heroes'];
  foreach ($report['settings']['heroes_exclude'] as $hid) {
    unset($meta['heroes'][$hid]);
  }
}