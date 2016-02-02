<?php

require_once 'ogm.civix.php';
require_once 'php/functions.php';

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
 * @param $op string, the type of operation being performed; 'check' or 'enqueue'
 * @param $queue CRM_Queue_Queue, (for 'enqueue') the modifiable list of pending up upgrade tasks
 *
 * @return mixed
 *   Based on op. for 'check', returns array(boolean) (TRUE if upgrades are pending)
 *                for 'enqueue', returns void
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
 */
function ogm_civicrm_buildForm($formName, &$form) {
  // custom hook: create or delete OGM session variables

  /* Events */

  if ($formName == 'CRM_Event_Form_Registration_Register') {
    // Remove OGM from tokens when done = TRUE.
    if (isset($_SESSION["CTRL"]["event"]["done"]) && $_SESSION["CTRL"]["event"]["done"]) {
      unset($_SESSION["CTRL"]["event"]);
    }
  }

  if ($formName == 'CRM_Event_Form_Registration_Confirm') {

    // Create OGM if OGM doesn't exist.
    if (!isset($_SESSION["CTRL"]["event"]["ogm"])) {
      $rand = rand(1, 999999);
      $ogm = createOGM($rand, $form->_eventId);
      $_SESSION["CTRL"]["event"]["ogm"] = '+++' . $ogm . '+++';
      $_SESSION["CTRL"]["event"]["done"] = FALSE;
    }

    if ($form->_flagSubmitted) {
      // Set done to true in session on thank you page.
      $_SESSION["CTRL"]["event"]["done"] = TRUE;
    }

  }

  /* Membership */

  if ($formName == 'CRM_Contribute_Form_Contribution_Main') {
    // Remove OGM from tokens when done = TRUE.
    if (isset($_SESSION["CTRL"]["membership"]["done"]) && $_SESSION["CTRL"]["membership"]["done"]) {
      unset($_SESSION["CTRL"]["membership"]);
    }
  }

  if ($formName == 'CRM_Contribute_Form_Contribution_Confirm') {
    // Create OGM if OGM doesn't exist.
    if (!isset($_SESSION["CTRL"]["membership"]["ogm"])) {
      $cid = 0;
      // Determine Contact id.
      if (isset($form->_membershipContactID)) {
        if ($form->_membershipContactID == 0) {
          // Logged in user use on behalf of id.
          $cid = $form->_params["select_contact_id"];
        }
        else {
          // Logged in user use CiviCRM id.
          $cid = $form->_membershipContactID;
        }
      }
      else {
        // Not logged in use random number.
        $cid = rand(1, 999999);
      }
      // Create OGM.
      $ogm = createOGM($cid, $form->_id);
      // Save to session.
      $_SESSION["CTRL"]["membership"]["ogm"] = '+++' . $ogm . '+++';
      $_SESSION["CTRL"]["membership"]["done"] = FALSE;
    }
  }

  if ($formName == 'CRM_Contribute_Form_Contribution_ThankYou') {
    // Set done to true in session.
    $_SESSION["CTRL"]["membership"]["done"] = TRUE;
  }

}

/**
 * Implements hook_civicrm_post().
 */
function ogm_civicrm_post($op, $objectName, $objectId, &$objectRef) {
  // log
  if ($objectName == "Contribution") {
    if ($op == "create") {

      /* Events */
      if (isset($_SESSION["CTRL"]["events"]["ogm"])) {
        // Only change source when event session var is set.
        $result = civicrm_api3('Contribution', 'create', array(
          'sequential' => 1,
          'id' => $objectId,
          'source' => $_SESSION["CTRL"]["event"]["ogm"],
        ));
      }

      /* Membership */
      if (isset($_SESSION["CTRL"]["membership"]["ogm"])) {
        // Only change source when membership session var is set.
        $result = civicrm_api3('Contribution', 'create', array(
          'sequential' => 1,
          'id' => $objectId,
          'source' => $_SESSION["CTRL"]["membership"]["ogm"],
        ));
      }

    }
  }
}

/**
 * Implements hook_civicrm_alterContent().
 */
function ogm_civicrm_alterContent(&$content, $context, $tplName, &$object) {
  // custom hook: change tokens in forms.

  /* Events */
  if ($context == "form") {
    if ($tplName == "CRM/Event/Form/Registration/Confirm.tpl" || $tplName == "CRM/Event/Form/Registration/ThankYou.tpl") {
      // Find & Replace token
      $content = str_replace('[token_ogm]', $_SESSION["CTRL"]["event"]["ogm"], $content);
    }
  }

  /* Membership */
  if ($context == "form") {
    if ($tplName == "CRM/Contribute/Form/Contribution/Confirm.tpl" || $tplName == "CRM/Contribute/Form/Contribution/ThankYou.tpl") {
      // Find & Replace token
      $content = str_replace('[token_ogm]', $_SESSION["CTRL"]["membership"]["ogm"], $content);
    }
  }

}

/**
 * Implements hook_civicrm_alterMailParams().
 */
function ogm_civicrm_alterMailParams(&$params, $context) {
  // custom hook: change tokens in "html" & "text" mailing formats.

  /* Events */
  if ($params['valueName'] == "membership_online_receipt") {
    $text = $params['text'];
    $params['text'] = str_replace('[token_ogm]', $_SESSION["CTRL"]["membership"]["ogm"], $text);
    $html = $params['html'];
    $params['html'] = str_replace('[token_ogm]', $_SESSION["CTRL"]["membership"]["ogm"], $html);
  }

  /* Membership */
  if ($params['valueName'] == "event_online_receipt") {
    $text = $params['text'];
    $params['text'] = str_replace('[token_ogm]', $_SESSION["CTRL"]["event"]["ogm"], $text);
    $html = $params['html'];
    $params['html'] = str_replace('[token_ogm]', $_SESSION["CTRL"]["event"]["ogm"], $html);
  }

  /* Log */
  watchdog('be.ctrl.ogm', 'hook alterMailParams: ' . print_r($params, TRUE));
}

