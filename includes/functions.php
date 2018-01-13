<?php

/**
 * Creates an OGM
 *
 * @param integer $var1 cid or random number
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
 * Replaces token(s) in a text
 *
 * @param string $content text containing tokens
 * @param string $qfKey session reference
 *
 * @return string
 */
function ogm_civicrm_replaceTokens($content, $qfKey) {
  $tokens = [
    '{ctrl.ogm}' => 'ctrl_ogm',
    '{ctrl.amount}' => 'ctrl_amount',
    '{ctrl.type}' => 'ctrl_type',
    '{ctrl.subject}' => 'ctrl_subject',
  ];

  foreach ($tokens as $key => $value) {
    if (isset($_SESSION["CTRL"][$qfKey][$value])) {
      $content = str_replace($key, $_SESSION["CTRL"][$qfKey][$value], $content);
    }
    else {
      $content = str_replace($key, "", $content);
    }
  }
  return $content;
}

/**
 * Fetches membership subject.
 *
 * @param integer
 *
 * @return string
 */
function ogm_civicrm_membership_subject($id) {
  // Set default to NULL.
  $membership_name = NULL;
  try{
    // Fetch membership_id by MembershipPayment API call.
    $result = civicrm_api3('MembershipPayment', 'get', [
      'sequential' => 1,
      'id' => $id,
    ]);
  } catch (Exception $e) {
    // log exception.
  }
  // With results continue.
  if (!$result['is_error'] && $result['count'] > 0) {
    $membership_id = $result['values'][0]['membership_id'];
    try {
      // Fetch membership_name by Membership API call.
      $membership = civicrm_api3('Membership', 'get', [
        'sequential' => 1,
        'id' => $membership_id,
      ]);
    } catch (Exception $e) {
      // log exception.
    }
    if (!$membership['is_error'] && $membership['count'] > 0) {
      $membership_name = $membership['values'][0]['membership_name'];
    }
  }
  // Return membership_name.
  return $membership_name;
}