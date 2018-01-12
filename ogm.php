<?php

require_once 'ogm.civix.php';
require_once 'php/ogm_functions.php';

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
 */
function ogm_civicrm_buildForm($formName, &$form) {

  // Custom hook: Create OGM as session variable.

  /* Events */
  if (strpos($formName, 'CRM_Event_Form_Registration_') !== FALSE) {
    // Reset Session if payment is failed.
    if ($_REQUEST["cc"] == 'fail') {
      // Unset session.
      unset($_SESSION["CTRL"]["event"]);
    }
    // Create OGM if OGM doesn't exist.
    if (!isset($_SESSION["CTRL"]["event"]["ogm"])) {
      $rand = rand(1, 999999);
      $ogm = ogm_civicrm_createOGM($rand, $form->_eventId);
      $_SESSION["CTRL"]["event"]["ogm"] = '+++' . $ogm . '+++';
    }
  }

  /* Memberships */
  if (strpos($formName, 'CRM_Contribute_Form_Contribution_') !== FALSE) {
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
      $ogm = ogm_civicrm_createOGM($cid, $form->_id);
      // Save to session.
      $_SESSION["CTRL"]["membership"]["ogm"] = '+++' . $ogm . '+++';
    }
  }

  /* Log
  if (strpos($formName, 'CRM_Event_Form_Registration_') !== FALSE || strpos($formName, 'CRM_Contribute_Form_Contribution_') !== FALSE) {
    // Reset session.
    // unset($_SESSION['CTRL']);
    // Log variables.
    // dpm($formName);
    // dpm($_REQUEST);
    // dpm($_SESSION);
  }
  */
}

/**
 * Implements hook_civicrm_post().
 */
function ogm_civicrm_post($op, $objectName, $objectId, &$objectRef) {

  // Custom hook: Get Email from submission.
  if ($objectName == "Email") {
    if ($op == "edit") {
      // Only fetch when event or membership session var is set.

      /* Events */
      if (isset($_SESSION["CTRL"]["event"]["ogm"])) {
        try {
          $result = civicrm_api3('Email', 'get', [
            'sequential' => 1,
            'id' => $objectId,
          ]);
        } catch (Exception $e) {
          Civi::log()->debug(__FUNCTION__);
        }
        // Set email in SESSION.
        if (!$result['is_error'] && $result['count'] > 0) {
          $email = $result['values'][0]['email'];
          $_SESSION["CTRL"]["event"]["email"] = $email;
        }
      }

      /* Memberships */
      if (isset($_SESSION["CTRL"]["membership"]["ogm"])) {
        try {
          $result = civicrm_api3('Email', 'get', [
            'sequential' => 1,
            'id' => $objectId,
          ]);
        } catch (Exception $e) {
          Civi::log()->debug(__FUNCTION__);
        }
        // Set email in SESSION.
        if (!$result['is_error'] && $result['count'] > 0) {
          $email = $result['values'][0]['email'];
          $_SESSION["CTRL"]["membership"]["email"] = $email;
        }
      }
    }
  }

  // Custom hook: Save OGM as source with the contribution.
  if ($objectName == "Contribution") {
    if ($op == "create") {

      /* Events */
      if (isset($_SESSION["CTRL"]["event"]["ogm"])) {
        // Only change source when event session var is set.
        try {
          $result = civicrm_api3('Contribution', 'create', [
            'sequential' => 1,
            'id' => $objectId,
            'source' => $_SESSION["CTRL"]["event"]["ogm"],
            'trxn_id' => $_SESSION["CTRL"]["event"]["ogm"],
            'contribution_note' => $_SESSION["CTRL"]["event"]["ogm"],
          ]);
        } catch (Exception $e) {
          Civi::log()->debug(__FUNCTION__);
        }
        // Set total_amount & receive_date in SESSION.
        if (!$result['is_error'] && $result['count'] > 0) {
          $_SESSION["CTRL"]["event"]["total_amount"] = $result['values'][0]['total_amount'];
          $_SESSION["CTRL"]["event"]["receive_date"] = $result['values'][0]['receive_date'];
        }
      }

      /* Memberships */
      if (isset($_SESSION["CTRL"]["membership"]["ogm"])) {
        // Only change source when membership session var is set.
        try {
          $result = civicrm_api3('Contribution', 'create', [
            'sequential' => 1,
            'id' => $objectId,
            'source' => $_SESSION["CTRL"]["membership"]["ogm"],
            'trxn_id' => $_SESSION["CTRL"]["membership"]["ogm"],
            'contribution_note' => $_SESSION["CTRL"]["membership"]["ogm"],
          ]);
        } catch (Exception $e) {
          Civi::log()->debug(__FUNCTION__);
        }
        // Set total_amount & receive_date in SESSION.
        if (!$result['is_error'] && $result['count'] > 0) {
          $_SESSION["CTRL"]["membership"]["total_amount"] = $result['values'][0]['total_amount'];
          $_SESSION["CTRL"]["membership"]["receive_date"] = $result['values'][0]['receive_date'];
        }
      }
    }
  }

  /*
   * A new membership works with the hook "membership" a renewal doesn't.
   * Changed "membership post" to "membershipPayment post" to solve this.
   */

  // Custom hook: Get MembershipPayment name from submission.
  if ($objectName == "MembershipPayment") {
    if ($op == "create") {
      // Only fetch when membership session var is set.
      if (isset($_SESSION["CTRL"]["membership"]["ogm"])) {
        try {
          $result = civicrm_api3('MembershipPayment', 'get', [
            'sequential' => 1,
            'id' => $objectId,
          ]);
        } catch (Exception $e) {
          Civi::log()->debug(__FUNCTION__);
        }
        // Set membership_name in SESSION.
        if (!$result['is_error'] && $result['count'] > 0) {
          // Fetch membership name.
          $membership_id = $result['values'][0]['membership_id'];
          try {
            $membership = civicrm_api3('Membership', 'get', [
              'sequential' => 1,
              'id' => $membership_id,
            ]);
          } catch (Exception $e) {
            Civi::log()->debug(__FUNCTION__);
          }
          if (!$membership['is_error'] && $membership['count'] > 0) {
            $membership_name = $membership['values'][0]['membership_name'];
            $_SESSION["CTRL"]["membership"]["membership_name"] = $membership_name;
          }
        }
      }
    }
  }

}

/**
 * Implements hook_civicrm_alterContent().
 */
function ogm_civicrm_alterContent(&$content, $context, $tplName, &$object) {
  // Custom hook: change tokens in forms.
  if ($context == "form") {

    /* Events */
    if ($tplName == "CRM/Event/Form/Registration/Confirm.tpl" || $tplName == "CRM/Event/Form/Registration/ThankYou.tpl") {
      // Find & Replace token
      $content = ogm_civicrm_replaceTokens($content, "event");
      // Unset session
      if ($tplName == "CRM/Event/Form/Registration/ThankYou.tpl") {
        // Unset session.
        unset($_SESSION["CTRL"]["event"]);
      }
    }

    /* Memberships */
    if ($tplName == "CRM/Contribute/Form/Contribution/Confirm.tpl" || $tplName == "CRM/Contribute/Form/Contribution/ThankYou.tpl") {
      // Find & Replace token.
      $content = ogm_civicrm_replaceTokens($content, "membership");
      // Unset session.
      if ($tplName == "CRM/Contribute/Form/Contribution/ThankYou.tpl") {
        // Unset session.
        unset($_SESSION["CTRL"]["membership"]);
      }
    }
  }
}

/**
 * Implements hook_civicrm_alterMailParams().
 */
function ogm_civicrm_alterMailParams(&$params, $context) {
  // Custom hook: change tokens in "html" & "text" mailing formats.

  /* Events */
  if (isset($params['valueName']) && $params['valueName'] == "event_online_receipt") {
    if (isset($_SESSION["CTRL"]["event"]["ogm"])) {
      // Plain text email.
      if(isset($params['text'])) {
        $params['text'] = ogm_civicrm_replaceTokens($params['text'], "event");
      }
      // HTML text email.
      if(isset($params['html'])) {
        $params['html'] = ogm_civicrm_replaceTokens($params['html'], "event");
      }
    }
  }

  /* Memberships */
  if (isset($params['valueName']) && $params['valueName'] == "membership_online_receipt") {
    if (isset($_SESSION["CTRL"]["membership"]["ogm"])) {
      // Plain text email.
      if (isset($params['text'])) {
        $params['text'] = ogm_civicrm_replaceTokens($params['text'], "membership");
      }
      // HTML text email.
      if (isset($params['html'])) {
        $params['html'] = ogm_civicrm_replaceTokens($params['html'], "membership");
      }
    }
  }

}

