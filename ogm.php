<?php

require_once 'ogm.civix.php';
require_once 'includes/functions.php';

/**
 * Implements hook_civicrm_config().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_config
 */
function ogm_civicrm_config(&$config) {
  _ogm_civix_civicrm_config($config);
}

/**
 * Implements hook_civicrm_install().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_install
 */
function ogm_civicrm_install() {
  _ogm_civix_civicrm_install();
}

/**
 * Implements hook_civicrm_enable().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_enable
 */
function ogm_civicrm_enable() {
  _ogm_civix_civicrm_enable();
}

/**
 * Implements hook_civicrm_pre().
 *
 * @param $op
 * @param $objectName
 * @param $id
 * @param $params
 */
function ogm_civicrm_pre($op, $objectName, $id, &$params) {
  // Custom hook: Save OGM as source with the contribution.
  if ($objectName == "Contribution") {
    if ($op == "create") {
      // Alter 'is_pay_later' contributions.
      if ($params['is_pay_later']) {
        // Alter all 'donation' payments.
        if (isset($params['financial_type_id']) && $params['financial_type_id'] == 1) {
          // Set contribution type.
          $contribution_type = ts('Donation');
          // Fetch 'contribution page' id from parameters.
          if (isset($params['contribution_page_id'])) {
            // Set subject_id if contribution page is known.
            $subject_id = $params['contribution_page_id'];
          }
        }
        // Alter all 'membership' payments.
        if (isset($params['financial_type_id']) && $params['financial_type_id'] == 2) {
          // Set contribution type.
          $contribution_type = ts('Membership');
          // Fetch 'contribution page' id from parameters.
          if (isset($params['contribution_page_id'])) {
            // Set subject_id if contribution page is known.
            $subject_id = $params['contribution_page_id'];
          }
          // Fetch 'membership' id from parameters.
          if (isset($params['membership_id'])) {
            // Set subject_id if membership id is known.
            $subject_id = $params['membership_id'];
          }
        }
        // Alter all 'event' payments.
        if (isset($params['financial_type_id']) && $params['financial_type_id'] == 4) {
          // Set contribution type.
          $contribution_type = ts('Event');
          // Fetch 'event' id from 'entryURL'.
          $url = parse_url(htmlspecialchars_decode($_REQUEST['entryURL']));
          parse_str($url['query'], $event);
          if (isset($event['id'])) {
            // Set subject_id if event id is known.
            $subject_id = $event['id'];
          }
          // Fetch 'event' id from request.
          if (isset($_REQUEST['event_id'])) {
            // Set subject_id if event id is known.
            $subject_id = $_REQUEST['event_id'];
          }
        }
        // Check for 'subject_id' & 'is_pay_later' contributions only?
        // If subject_id is set create OGM & Session.
        if (isset($subject_id)) {
          // Fetch 'contact_id' parameter.
          $contact_id = rand(1, 999999);
          if (isset($params['contact_id'])) {
            $contact_id = $params['contact_id'];
          }
          // Generate OGM code.
          $ogm = ogm_civicrm_createOGM($contact_id, $subject_id);
          // Add OGM code to contribution
          $params['source'] = $ogm;
          $params['trxn_id'] = $ogm;
          // Fetch qfKey.
          $qfKey = $_REQUEST['qfKey'];
          // Create Session variables.
          $_SESSION['ctrl'][$qfKey]['contribution_ogm'] = $ogm;
          $_SESSION['ctrl'][$qfKey]['contribution_amount'] = CRM_Utils_Money::format($params['total_amount'], $params['currency']);
          $_SESSION['ctrl'][$qfKey]['contribution_type'] = $contribution_type;
        }
      }
    }
  }
}

/**
 * Implements hook_civicrm_alterContent().
 *
 * @param $content
 * @param $context
 * @param $tplName
 * @param $object
 *
 */
function ogm_civicrm_alterContent(&$content, $context, $tplName, &$object) {
  // Change tokens in forms.
  if ($context == "form" && isset($_REQUEST['qfKey'])) {
    // Replace Tokens.
    if ($tplName == "CRM/Event/Form/Registration/Confirm.tpl"
      || $tplName == "CRM/Event/Form/Registration/ThankYou.tpl"
      || $tplName == "CRM/Contribute/Form/Contribution/Confirm.tpl"
      || $tplName == "CRM/Contribute/Form/Contribution/ThankYou.tpl") {
      // Find & Replace token
      $content = ogm_civicrm_replaceTokens($content, $_REQUEST['qfKey']);
    }
    // Unset session by qfKey.
    if ($tplName == "CRM/Event/Form/Registration/ThankYou.tpl" || $tplName == "CRM/Contribute/Form/Contribution/ThankYou.tpl") {
      // Fetch qfKey.
      $qfKey = $_REQUEST['qfKey'];
      unset($_SESSION['ctrl'][$qfKey]);
    }
  }
}

/**
 * Implements hook_civicrm_alterMailParams().
 *
 * @param $params
 * @param $context
 *
 */
function ogm_civicrm_alterMailParams(&$params, $context) {
  // Change tokens in "html" & "text" mailing formats.
  /* Events & Memberships */
  if (isset($params['valueName']) && isset($_REQUEST['qfKey'])) {
    if ($params['valueName'] == "event_online_receipt" || $params['valueName'] == "membership_online_receipt") {
      // Fetch qfKey.
      $qfKey = $_REQUEST['qfKey'];
      // Plain text email.
      if (isset($params['text'])) {
        $params['text'] = ogm_civicrm_replaceTokens($params['text'], $qfKey);
      }
      // HTML text email.
      if (isset($params['html'])) {
        $params['html'] = ogm_civicrm_replaceTokens($params['html'], $qfKey);
      }
    }
  }
  /* CiviCRM Rules */
  if (isset($params['groupName']) && isset($_REQUEST['qfKey'])) {
    if ($params['groupName'] == 'E-mail from API' || $params['groupName'] == 'Email from API') {
      // Fetch qfKey.
      $qfKey = $_REQUEST['qfKey'];
      // Plain text email.
      if (isset($params['text'])) {
        $params['text'] = ogm_civicrm_replaceTokens($params['text'], $qfKey);
      }
      // HTML text email.
      if (isset($params['html'])) {
        $params['html'] = ogm_civicrm_replaceTokens($params['html'], $qfKey);
      }
    }
  }
}
