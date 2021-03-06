<?php
$sql = "SELECT start_date FROM matches ORDER BY start_date ASC LIMIT 1;";

if ($conn->multi_query($sql) === FALSE) die("[F] Unexpected problems when requesting database.\n".$conn->error."\n");
$query_res = $conn->store_result();
$start_timestamp = $query_res->fetch_row()[0] - 3600;

$query_res->free_result();

$result["days"] = array();
# 86400 = day = 3600*24
$sql = "SELECT start_date, ( (start_date-$start_timestamp) DIV 86400 ) day FROM matches GROUP BY day;";

if ($conn->multi_query($sql) === TRUE) echo "[S] Requested data for DAYS.\n";
else die("[F] Unexpected problems when requesting database.\n".$conn->error."\n");

$query_res = $conn->store_result();

for ($row = $query_res->fetch_row(); $row != null; $row = $query_res->fetch_row()) {
  $result["days"][$row[1]] = array(
    "timestamp" => $start_timestamp + 86400*$row[1],
    "matches_num" => 0
  );
}

$query_res->free_result();

if ($lg_settings['ana']['matchlist']) {
  foreach($result["days"] as $day => $date) {
    $result["days"][$day]['matches'] = [];
    $sql = "SELECT matchid FROM matches WHERE start_date >= ".$date['timestamp']." AND start_date < ".($date['timestamp']+86400).";";

    if ($conn->multi_query($sql) === FALSE) die("[F] Unexpected problems when requesting database.\n".$conn->error."\n");

    $query_res = $conn->store_result();

    for ($row = $query_res->fetch_row(); $row != null; $row = $query_res->fetch_row()) {
      $result["days"][$day]['matches'][] = $row[0];
      $result["days"][$day]['matches_num']++;
    }

    $query_res->free_result();
  }
} else {
  foreach($result["days"] as $day => $date) {
    $sql = "SELECT COUNT(DISTINCT matchid) FROM matches WHERE start_date >= ".$date['timestamp']." AND start_date < ".($date['timestamp']+86400).";";

    if ($conn->multi_query($sql) === FALSE) die("[F] Unexpected problems when requesting database.\n".$conn->error."\n");

    $query_res = $conn->store_result();

    for ($row = $query_res->fetch_row(); $row != null; $row = $query_res->fetch_row()) {
      $result["days"][$day]['matches_num'] += $row[0];
    }

    $query_res->free_result();
  }
}
?>
