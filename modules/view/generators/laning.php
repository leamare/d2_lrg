<?php

include_once($root."/modules/view/functions/hero_name.php");
include_once($root."/modules/view/functions/player_name.php");

function rg_generator_laning_profile($table_id, &$context, $id_o, $heroes_flag = true) {
  $res = "";

  $i = 0;

  if(!sizeof($context)) return "";

  $ranks = [];
  $ids = [ 0 ];
  if (!empty($id_o)) $ids[] = $id_o;

  foreach ($ids as $id) {
    uasort($context[$id], function($a, $b) {
      $aa = (float)($a['lane_wr']);
      $bb = (float)($b['lane_wr']);
      return $bb <=> $aa;
    });

    $mm = 0;
    foreach ($context[$id] as $h) {
      if ($h['matches'] > $mm) $mm = $h['matches'];
    }

    $compound_ranking_sort = function($a, $b) use ($mm) {
      if ($a['matches'] == 0) return 1;
      if ($b['matches'] == 0) return -1;
      return compound_ranking_laning_sort($a, $b, $mm);
    };
    uasort($context[$id], $compound_ranking_sort);

    $increment = 100 / sizeof($context[$id]); $i = 0;

    foreach ($context[$id] as $elid => $el) {
      if(isset($last) && $el == $last) {
        $i++;
        $context[$id][$elid]['rank'] = $last_rank;
      } else
        $context[$id][$elid]['rank'] = round(100 - $increment*$i++, 2);
      $last = $el;
      $last_rank = $context[$id][$elid]['rank'];
    }

    unset($last);
  }



  if ($id) {
    $res = "<table id=\"$table_id\" class=\"list wide\"><caption>".locale_string("laning_reference")."</caption>";
    $res .= "<thead><tr class=\"overhead\">".
            "<th width=\"12%\" colspan=\"".($heroes_flag ? "2" : "1")."\"></th>".
            "<th width=\"18%\" colspan=\"3\"></th>".
            "<th class=\"separator\" colspan=\"2\">".locale_string("lane_advantage")."</th>".
            "<th class=\"separator\" colspan=\"2\">".locale_string("lane_won")."</th>".
            "<th class=\"separator\" colspan=\"2\">".locale_string("lane_tie")."</th>".
            "<th class=\"separator\" colspan=\"2\">".locale_string("lane_loss")."</th>".
          "</tr><tr>".
          ($heroes_flag ? "<th width=\"1%\"></th>" : "").
          "<th>".locale_string($id ? "opponent" : "hero")."</th>".
          "<th>".locale_string("matches")."</th>".
          "<th>".locale_string("lane_wr")."</th>".
          "<th>".locale_string("rank")."</th>".
          "<th>".locale_string("lane_win")."</th>".
          "<th>".locale_string("lane_loss")."</th>".

          "<th>".locale_string("ratio_freq")."</th>".
          "<th>".locale_string("lane_game_won")."</th>".
          "<th>".locale_string("ratio_freq")."</th>".
          "<th>".locale_string("lane_game_won")."</th>".
          "<th>".locale_string("ratio_freq")."</th>".
          "<th>".locale_string("lane_game_won")."</th>".
        "</tr></thead>";

    $data = $context[0][$id_o];
    $res .= "<tr>".
      ($heroes_flag ? "<td>".hero_portrait($elid)."</td>" : '').
      "<td>".($heroes_flag ? hero_name($elid) : player_name($elid))."</td>".
      "<td>".($data['matches'] ? $data['matches'] : '-')."</td>".
      "<td>".($data['matches'] ? number_format($data['lane_wr']*100, 2).'%' : '-')."</td>".
      "<td>".($data['matches'] ? number_format($data['rank'], 1) : '0')."</td>".
      "<td>".($data['matches'] ? number_format($data['avg_advantage']*100, 2).'%' : '-')."</td>".
      "<td>".($data['matches'] ? number_format($data['avg_disadvantage']*100, 2).'%' : '-')."</td>".

      "<td>".($data['matches'] ? number_format($data['lanes_won']*100/$data['matches'], 2).'%' : '-')."</td>".
      "<td>".($data['matches'] ? ($data['lanes_won'] ? number_format($data['won_from_won']*100/$data['lanes_won'], 2) : '0').'%' : '-')."</td>".

      "<td>".($data['matches'] ? number_format($data['lanes_tied']*100/$data['matches'], 2).'%' : '-')."</td>".
      "<td>".($data['matches'] ? ($data['lanes_tied'] ? number_format($data['won_from_tie']*100/$data['lanes_tied'], 2) : '0').'%' : '-')."</td>".

      "<td>".($data['matches'] ? number_format($data['lanes_lost']*100/$data['matches'], 2).'%' : '-')."</td>".
      "<td>".($data['matches'] ? ($data['lanes_lost'] ? number_format($data['won_from_behind']*100/$data['lanes_lost'], 2) : '0').'%' : '-')."</td>".
    "</tr>";
    

    $res .= "</table>";
  }

  $res .= "<table id=\"$table_id\" class=\"list wide sortable\">";
  $res .= "<thead><tr class=\"overhead\">".
            "<th width=\"12%\" colspan=\"".($heroes_flag ? "2" : "1")."\"></th>".
            "<th width=\"18%\" colspan=\"3\"></th>".
            "<th class=\"separator\" colspan=\"2\" data-sorter=\"digit\">".locale_string("lane_advantage")."</th>".
            "<th class=\"separator\" colspan=\"2\" data-sorter=\"digit\">".locale_string("lane_won")."</th>".
            "<th class=\"separator\" colspan=\"2\" data-sorter=\"digit\">".locale_string("lane_tie")."</th>".
            "<th class=\"separator\" colspan=\"2\" data-sorter=\"digit\">".locale_string("lane_loss")."</th>".
          "</tr><tr>".
          ($heroes_flag ? "<th width=\"1%\"></th>" : "").
          "<th data-sortInitialOrder=\"asc\" data-sorter=\"text\">".locale_string($id ? "opponent" : "hero")."</th>".
          "<th data-sorter=\"digit\">".locale_string("matches")."</th>".
          "<th data-sorter=\"digit\">".locale_string("lane_wr")."</th>".
          "<th data-sorter=\"digit\">".locale_string("rank")."</th>".
          "<th data-sorter=\"digit\">".locale_string("lane_win")."</th>".
          "<th data-sorter=\"digit\">".locale_string("lane_loss")."</th>".

          "<th data-sorter=\"digit\">".locale_string("ratio_freq")."</th>".
          "<th data-sorter=\"digit\">".locale_string("lane_game_won")."</th>".
          "<th data-sorter=\"digit\">".locale_string("ratio_freq")."</th>".
          "<th data-sorter=\"digit\">".locale_string("lane_game_won")."</th>".
          "<th data-sorter=\"digit\">".locale_string("ratio_freq")."</th>".
          "<th data-sorter=\"digit\">".locale_string("lane_game_won")."</th>".
        "</tr></thead>";

  foreach($context[$id_o] as $elid => $data) {
    $res .= "<tr>".
      ($heroes_flag ? "<td>".hero_portrait($elid)."</td>" : '').
      "<td>".($heroes_flag ? hero_name($elid) : player_name($elid))."</td>".
      "<td>".($data['matches'] ? $data['matches'] : '-')."</td>".
      "<td>".($data['matches'] ? number_format($data['lane_wr']*100, 2).'%' : '-')."</td>".
      "<td>".($data['matches'] ? number_format($data['rank'], 1) : '0')."</td>".
      "<td>".($data['matches'] ? number_format($data['avg_advantage']*100, 2).'%' : '-')."</td>".
      "<td>".($data['matches'] ? number_format($data['avg_disadvantage']*100, 2).'%' : '-')."</td>".

      "<td>".($data['matches'] ? number_format($data['lanes_won']*100/$data['matches'], 2).'%' : '-')."</td>".
      "<td>".($data['matches'] ? ($data['lanes_won'] ? number_format($data['won_from_won']*100/$data['lanes_won'], 2) : '0').'%' : '-')."</td>".

      "<td>".($data['matches'] ? number_format($data['lanes_tied']*100/$data['matches'], 2).'%' : '-')."</td>".
      "<td>".($data['matches'] ? ($data['lanes_tied'] ? number_format($data['won_from_tie']*100/$data['lanes_tied'], 2) : '0').'%' : '-')."</td>".

      "<td>".($data['matches'] ? number_format($data['lanes_lost']*100/$data['matches'], 2).'%' : '-')."</td>".
      "<td>".($data['matches'] ? ($data['lanes_lost'] ? number_format($data['won_from_behind']*100/$data['lanes_lost'], 2) : '0').'%' : '-')."</td>".
    "</tr>";
  }

  $res .= "</table>";

  return $res;
}