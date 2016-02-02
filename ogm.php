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
  watchdog('be.ctrl.ogm', 'hook buildform: ' . print_r($formName, TRUE));

  /* Events */
  if ($formName == 'CRM_Event_Form_Registration_Confirm') {
    if (!$form->_flagSubmitted) {
      // reset/unset session variable
      unset($_SESSION["CTRL"]["events"]);
    }
    else {
      // create ogm
      $rand = rand(1, 999999);
      $ogm = createOGM($rand, $form->_eventId);
      $_SESSION["CTRL"]["events"]["ogm"] = '+++' . $ogm . '+++';
    }
  }

  /* Membership */
  if ($formName == 'CRM_Contribute_Form_Contribution_Confirm') {

    dpm($_SESSION);
    dpm($formName);
    dpm($form);

    if (!$form->_flagSubmitted) {
      dpm("false");
      // reset/unset session variable
      unset($_SESSION["CTRL"]["membership"]);
    }
    else {

      dpm("true");
      // create ogm when step = Confirmation
      $rand = rand(1, 999999);
      $ogm = createOGM($rand, $form->_id);
      dpm($ogm);
      $_SESSION["CTRL"]["membership"]["ogm"] = '+++' . $ogm . '+++';
    }
  }

}

/**
 * Implements hook_civicrm_alterMailParams().
 */
function ogm_civicrm_alterMailParams(&$params, $context) {
  // custom hook: change tokens in "html" & "text" mailing formats.
  watchdog('be.ctrl.ogm', 'hook alterMailParams: ' . print_r($params, TRUE));

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
    $params['text'] = str_replace('[token_ogm]', $_SESSION["CTRL"]["events"]["ogm"], $text);
    $html = $params['html'];
    $params['html'] = str_replace('[token_ogm]', $_SESSION["CTRL"]["events"]["ogm"], $html);
  }

}

