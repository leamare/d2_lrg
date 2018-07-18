<?php

$modules['heroes']['combos'] = [];

function rg_view_generate_heroes_combos() {
  global $report, $parent, $root, $unset_module, $mod;
  if($mod == $parent."combos") $unset_module = true;
  $parent_module = $parent."combos-";
  $res = [];

  if(isset($report['hero_pairs'])) {
    $res['pairs'] = "";
    if (check_module($parent_module."pairs")) {
      include_once($root."/modules/view/generators/pairs.php");
      $res['pairs'] =  "<div class=\"content-text\">".locale_string("desc_heroes_pairs", [ "limh"=>$report['settings']['limiter']+1 ] )."</div>";
      $res['pairs'] .=  rg_generator_pairs("hero-pairs",
                                         $report['hero_pairs'],
                                         (isset($report['hero_pairs_matches']) ? $report['hero_pairs_matches'] : [])
                                       );
    }
  }
  if(isset($report['hero_triplets']) && !empty($report['hero_triplets'])) {
    $res['trios'] = "";
    if (check_module($parent_module."trios")) {
      include_once($root."/modules/view/generators/trios.php");
      $res['trios'] =  "<div class=\"content-text\">".locale_string("desc_heroes_trios", [ "liml"=>$report['settings']['limiter_triplets']+1 ] )."</div>";
      $res['trios'] .= rg_generator_trios("hero-trios",
                                         $report['hero_triplets'],
                                         (isset($report['hero_triplets_matches']) ? $report['hero_triplets_matches'] : [])
                                       );
    }
  }
  if(isset($report['hero_lane_combos'])) {
    $res['lane_combos'] = "";
    if (check_module($parent_module."lane_combos")) {
      include_once($root."/modules/view/generators/pairs.php");
      $res['lane_combos'] =  "<div class=\"content-text\">".locale_string("desc_heroes_lane_combos", [ "liml"=>$report['settings']['limiter_triplets']+1 ] )."</div>";
      $res['lane_combos'] .=  rg_generator_pairs("hero-lanecombos", $report['hero_lane_combos'], []);
    }
  }

  return $res;
}

?>