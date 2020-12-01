<?php
function hero_portrait($hid) {
  global $meta, $portraits_provider;
  if (isset($meta->heroes[ $hid ]['tag']))
    return "<img class=\"hero_portrait\" src=\"".str_replace("%HERO%", $meta['heroes'][ $hid ]['tag'], $portraits_provider)."\" alt=\"".$meta['heroes'][ $hid ]['tag']."\" />";
  else return "<img class=\"hero_portrait\" alt=\"undefined\" />";
}

function hero_icon_link($hid) {
  global $meta, $icons_provider;
  if (isset($meta->heroes[ $hid ]['tag']))
    return str_replace("%HERO%", $meta['heroes'][ $hid ]['tag'], $icons_provider);
  return str_replace("%HERO%", 'default', $icons_provider);
}

function hero_icon($hid) {
  global $meta, $icons_provider;
  if (isset($meta->heroes[ $hid ]['tag']))
    return "<img class=\"hero_icon\" src=\"".hero_icon_link($hid)."\" alt=\"".$meta['heroes'][ $hid ]['tag']."\" />";
  else return "<img class=\"hero_icon\" alt=\"undefined\" />";
}

function hero_full($hid) {
  return hero_portrait($hid)." ".hero_name($hid);
}

function hero_full_icon($hid) {
  return hero_icon($hid)." ".hero_name($hid);
}

function hero_name($hid) {
  global $meta;
  if (isset($meta->heroes[ $hid ]['name'])) return $meta->heroes[ $hid ]['name'];
  return "undefined";
}

function hero_tag($hid) {
  global $meta;
  if (isset($meta->heroes[ $hid ]['tag'])) return $meta->heroes[ $hid ]['tag'];
  return "undefined";
}