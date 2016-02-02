<?php
/**
 * CiviCRM functions
 */

/**
 *
 * Creates an OGM
 *
 * @param integer $var1 cid or random
 * @param integer $var2 subject id
 * @return string
 */
function createOGM($var1, $var2) {
  $ten = substr(1000000 + $var1, -4) . substr(100 + $var1 % 97, -2) . substr(100 + $var2 % 97, -2) . substr(100 + date("Ymdhis") % 97, -2);
  $check = substr(100 + $ten % 97, -2);
  if ($check == "00") {
    $check = 97;
  }
  $ogm = substr($ten, 0, 3) . "/" . substr($ten, 3, 4) . "/" . substr($ten, 7, 3) . $check;
  return $ogm;
}