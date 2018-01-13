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
  unset($_SESSION['CTRL']);
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

      // TODO: Check for 'pending' contributions only?

      // Fetch 'contact_id' parameter.
      if (isset($params['contact_id'])) {
        $contact_id = $params['contact_id'];
      }
      else {
        $contact_id = rand(1, 999999);
      }
      // Fetch 'subject' id for contribution page.
      if (isset($params['contribution_page_id'])) {
        $subject_id = $params['contribution_page_id'];
      }

      // TODO: Check for event id!? (url param?)

      // If subject_id is set create OGM.
      if (isset($subject_id)) {
        // Generate OGM code.
        $ogm = ogm_civicrm_createOGM($contact_id, $subject_id);
        // Add OGM code to contribution
        $params['source'] = $ogm;
        $params['trxn_id'] = $ogm;
      }
    }
  }
}

/**
 * Implements hook_civicrm_post().
 *
 * @param $op
 * @param $objectName
 * @param $objectId
 * @param $objectRef
 */
function ogm_civicrm_post($op, $objectName, $objectId, &$objectRef) {

  // Create session parameters for Contribution.
  if ($objectName == "Contribution") {
    if ($op == "create") {
      if (isset($objectRef->trxn_id)) {
        // On 'civicrm_post' add parameters to Session.
        $qfKey = $_REQUEST['qfKey'];
        $_SESSION['CTRL'][$qfKey]['ctrl_id'] = $objectRef->id;
        $_SESSION['CTRL'][$qfKey]['ctrl_ogm'] = $objectRef->trxn_id;
        $_SESSION['CTRL'][$qfKey]['ctrl_amount'] = $objectRef->total_amount;
        // Based on financial_type_id.
        if ($objectRef->financial_type_id == 2) {
          // Set membership subject.
          $_SESSION['CTRL'][$qfKey]['ctrl_type'] = 'Membership';
        }
        if ($objectRef->financial_type_id == 4) {
          // Set event subject.
          $_SESSION['CTRL'][$qfKey]['ctrl_type'] = 'Event';
        }
      }
    }
  }

  // Create session parameters for MembershipPayment.
  if ($objectName == "MembershipPayment") {
    if ($op == "create") {
      $qfKey = $_REQUEST['qfKey'];
      // When contribution_id exist in session, add subject to session.
      if ($objectRef->contribution_id == $_SESSION['CTRL'][$qfKey]['ctrl_id']) {
        // Set membership subject.
        $_SESSION['CTRL'][$qfKey]['ctrl_subject'] = ogm_civicrm_membership_subject($objectId);
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
