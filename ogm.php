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
 * Implements hook_civicrm_xmlMenu().
 *
 * @param $files array(string)
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_xmlMenu
 */
function ogm_civicrm_xmlMenu(&$files) {
  _ogm_civix_civicrm_xmlMenu($files);
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
 * Implements hook_civicrm_uninstall().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_uninstall
 */
function ogm_civicrm_uninstall() {
  _ogm_civix_civicrm_uninstall();
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
 * Implements hook_civicrm_disable().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_disable
 */
function ogm_civicrm_disable() {
  _ogm_civix_civicrm_disable();
}

/**
 * Implements hook_civicrm_upgrade().
 *
 * @param $op string, the type of operation being performed; 'check' or
 *   'enqueue'
 * @param $queue CRM_Queue_Queue, (for 'enqueue') the modifiable list of
 *   pending up upgrade tasks
 *
 * @return mixed
 *   Based on op. for 'check', returns array(boolean) (TRUE if upgrades are
 *   pending) for 'enqueue', returns void
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_upgrade
 */
function ogm_civicrm_upgrade($op, CRM_Queue_Queue $queue = NULL) {
  return _ogm_civix_civicrm_upgrade($op, $queue);
}

/**
 * Implements hook_civicrm_managed().
 *
 * Generate a list of entities to create/deactivate/delete when this module
 * is installed, disabled, uninstalled.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_managed
 */
function ogm_civicrm_managed(&$entities) {
  _ogm_civix_civicrm_managed($entities);
}

/**
 * Implements hook_civicrm_caseTypes().
 *
 * Generate a list of case-types
 *
 * Note: This hook only runs in CiviCRM 4.4+.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_caseTypes
 */
function ogm_civicrm_caseTypes(&$caseTypes) {
  _ogm_civix_civicrm_caseTypes($caseTypes);
}

/**
 * Implements hook_civicrm_angularModules().
 *
 * Generate a list of Angular modules.
 *
 * Note: This hook only runs in CiviCRM 4.5+. It may
 * use features only available in v4.6+.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_caseTypes
 */
function ogm_civicrm_angularModules(&$angularModules) {
  _ogm_civix_civicrm_angularModules($angularModules);
}

/**
 * Implements hook_civicrm_alterSettingsFolders().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_alterSettingsFolders
 */
function ogm_civicrm_alterSettingsFolders(&$metaDataFolders = NULL) {
  _ogm_civix_civicrm_alterSettingsFolders($metaDataFolders);
}

/**
 * Implements hook_civicrm_buildForm().
 *
 * @param $formName
 * @param $form
 */
function ogm_civicrm_buildForm($formName, &$form) {

  /*
  // TODO: Clear OGM session for failed payments, needed qfKey & cc failed?
  if ($_REQUEST["cc"] == 'fail') {
    // Unset session.
    $qfKey = $_REQUEST['qfKey'];
    unset($_SESSION["CTRL"][qfKey]);
  }
  */

  /*
  // Development purpose.
  // unset($_SESSION['CTRL']);
  if (strpos($formName, 'CRM_Contribute_Form_Contribution_') !== FALSE || strpos($formName, 'CRM_Event_Form_Registration_') !== FALSE) {
    dpm($_SESSION['CTRL']);
  }
  */

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

      // Fetch 'contact_id' parameter.
      if (isset($params['contact_id'])) {
        $contact_id = $params['contact_id'];
      }
      else {
        $contact_id = rand(1, 999999);
      }

      // Fetch 'subject' id from contribution page id.
      if (isset($params['contribution_page_id'])) {
        $subject_id = $params['contribution_page_id'];
      }

      // Fetch 'event' id from request 'entryURL'
      if (isset($params['financial_type_id']) && $params['financial_type_id'] == 4) {
        $url = parse_url(htmlspecialchars_decode($_REQUEST['entryURL']));
        parse_str($url['query'], $query);
        if (isset($query['id'])) {
          $subject_id = $query['id'];
        }
        else {
          $subject_id = 0;
        }
      }

      // Check for 'subject_id' & contributions only?
      // If subject_id is set create OGM & Session.
      if (isset($subject_id) && $params['is_pay_later']) {
        // Generate OGM code.
        $ogm = ogm_civicrm_createOGM($contact_id, $subject_id);

        // Add OGM code to contribution
        $params['source'] = $ogm;
        $params['trxn_id'] = $ogm;

        // Fetch qfKey.
        $qfKey = $_REQUEST['qfKey'];

        // Create Session variables.
        $_SESSION['CTRL'][$qfKey]['contribution_ogm'] = $ogm;
        $_SESSION['CTRL'][$qfKey]['contribution_amount'] = CRM_Utils_Money::format($params['total_amount'], $params['currency']);

        // Based on financial_type_id.
        if ($params['financial_type_id'] == 2) {
          // Set membership subject.
          $_SESSION['CTRL'][$qfKey]['contribution_type'] = ts('Membership');
        }
        if ($params['financial_type_id'] == 4) {
          // Set event subject.
          $_SESSION['CTRL'][$qfKey]['contribution_type'] = ts('Event');
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
      unset($_SESSION['CTRL'][$qfKey]);
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
      // Plain text email.
      if (isset($params['text'])) {
        $params['text'] = ogm_civicrm_replaceTokens($params['text'], $_REQUEST['qfKey']);
      }
      // HTML text email.
      if (isset($params['html'])) {
        $params['html'] = ogm_civicrm_replaceTokens($params['html'], $_REQUEST['qfKey']);
      }
    }
  }

  /* CiviCRM Rules */
  if (isset($params['groupName']) && isset($_REQUEST['qfKey'])) {
    if ($params['groupName'] == 'E-mail from API') {
      // Plain text email.
      if (isset($params['text'])) {
        $params['text'] = ogm_civicrm_replaceTokens($params['text'], $_REQUEST['qfKey']);
      }
      // HTML text email.
      if (isset($params['html'])) {
        $params['html'] = ogm_civicrm_replaceTokens($params['html'], $_REQUEST['qfKey']);
      }
    }
  }

}
