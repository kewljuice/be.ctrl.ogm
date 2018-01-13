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
 *
 * @return string
 */
function ogm_civicrm_createOGM($var1, $var2) {
  $ten = substr(1000000 + $var1, -4) . substr(100 + $var1 % 97, -2) . substr(100 + $var2 % 97, -2) . substr(100 + date("Ymdhis") % 97, -2);
  $check = substr(100 + $ten % 97, -2);
  if ($check == "00") {
    $check = 97;
  }
  $ogm = substr($ten, 0, 3) . "/" . substr($ten, 3, 4) . "/" . substr($ten, 7, 3) . $check;
  return $ogm;
}

/**
 *
 * Replaces token(s) in a text
 *
 * @param string $var1 text with tokens
 * @param string $var2 context
 *
 * @return string
 */
function ogm_civicrm_replaceTokens($var1, $var2) {
  $tokens = [
    '[token_ogm]' => 'ogm',
  ];

  foreach ($tokens as $key => $value) {
    if (isset($_SESSION["CTRL"][$var2][$value])) {
      $var1 = str_replace($key, $_SESSION["CTRL"][$var2][$value], $var1);
    }
  }

  return $var1;
}
