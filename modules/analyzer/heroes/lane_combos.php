<?php
$result["hero_lane_combos"] = [];

$sql = "SELECT fm1.heroid, fm2.heroid,
          COUNT(distinct fm1.matchid) match_count,
          SUM(NOT matches.radiantWin XOR fm1.isRadiant)/SUM(1) winrate,
          fm1.lane lane
        FROM
          ( select m1.matchid, m1.heroid, am1.lane, m1.isRadiant
            from matchlines m1 JOIN adv_matchlines am1
            ON m1.matchid = am1.matchid AND m1.heroid = am1.heroid ) fm1
        JOIN
          ( select m2.matchid, m2.heroid, am2.lane, m2.isRadiant
            from matchlines m2 JOIN adv_matchlines am2
            ON m2.matchid = am2.matchid AND m2.heroid = am2.heroid ) fm2
        ON fm1.matchid = fm2.matchid and fm1.isRadiant = fm2.isRadiant and fm1.heroid < fm2.heroid
        JOIN matches ON fm1.matchid = matches.matchid
        WHERE fm1.lane = fm2.lane
        GROUP BY fm1.heroid, fm2.heroid
        HAVING match_count > $limiter_lower
        ORDER BY match_count DESC, winrate DESC;";
# limiting match count for hero pair to 3:
# 1 match = every possible pair
# 2 matches = may be a coincedence

if ($conn->multi_query($sql) === TRUE) echo "[S] Requested data for HERO LANE COMBOS.\n";
else die("[F] Unexpected problems when requesting database.\n".$conn->error."\n");

$query_res = $conn->store_result();

for ($row = $query_res->fetch_row(); $row != null; $row = $query_res->fetch_row()) {
  $result["hero_lane_combos"][] = [
    "heroid1" => $row[0],
    "heroid2" => $row[1],
    "matches" => $row[2],
    "winrate" => $row[3],
    "lane" => $row[4]
  ];
}

$query_res->free_result();
?>
