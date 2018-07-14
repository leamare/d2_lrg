<?php

$modules['heroes']['positions'] = [];

function rg_view_generate_heroes_positions() {
  global $report, $parent, $root, $meta, $strings;
  $parent_module = $parent."positions-";

  $res = [];
  $res["overview"] = "";
  for ($i=1; $i>=0; $i--) {
    for ($j=1; $j<6 && $j>0; $j++) {
      if (!$i) { $j = 0; }
      if(sizeof($report['hero_positions'][$i][$j]))
        $res["positions_$i.$j"]  = "";

      if (!$i) { break; }
    }
  }

  if (check_module($parent_module."overview")) {
    include_once($root."/modules/view/generators/positions_overview.php");
    $res["overview"] = rg_generator_positions_overview("heroes-positions-overview", $report['hero_positions']);
    $res["overview"] .= "<div class=\"content-text\">".locale_string("desc_heroes_positions")."</div>";
  } else {
    include_once($root."/modules/view/generators/summary.php");

    for ($i=1; $i>=0; $i--) {
      for ($j=1; $j<6 && $j>0; $j++) {
        if (!$i) { $j = 0; }

        if (!check_module($parent_module."positions_$i.$j")) {
          if (!$i) { break; }
          continue;
        }

        $res["positions_$i.$j"] = rg_generator_summary("heroes-positions-$i-$j", $report['hero_positions'][$i][$j]);

        $res["positions_$i.$j"] .= "<div class=\"content-text\">".locale_string("desc_heroes_positions")."</div>";
        if (!$i) { break; }
      }
    }
  }

  return $res;
}

?>
