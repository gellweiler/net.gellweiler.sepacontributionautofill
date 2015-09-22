<?php

require_once 'sepacontributionautofill.civix.php';

/**
 * Implements hook_civicrm_buildForm().
 *
 * Set's default values for custom sepa fields
 * in contribution form.
 */
function sepacontributionautofill_civicrm_buildForm($formName, &$form) {
  if (
    $formName == 'CRM_Contribute_Form_Contribution'
  ) {

    $banking_fields = sepacontributionautofill_get_custom_fields_from_table
      ('civicrm_value_bankverbindung_1');

    $sepa_fields = sepacontributionautofill_get_custom_fields_from_table
      ('civicrm_sepa_custom');

    // Get information about the contact this contribution is connected with.
    $contact = civicrm_api3('Contact', 'getsingle', array(
      'sequential' => 1,
      'return' => "external_identifier, " . implode(',', $banking_fields),
      'id' => $form->getContactId(),
    ));

    // Autofill sepa data with information from contact.

    // Use the external id as mandate id.
    $defaults["{$sepa_fields['reference']}_-1"] = $contact['external_identifier'];

    // Get iban and bic from custom banking data fields.
    $defaults["{$sepa_fields['iban']}_-1"] = $contact[$banking_fields['iban_2']];
    $defaults["{$sepa_fields['bic']}_-1"] = $contact[$banking_fields['bic_3']];

    $form->setDefaults($defaults);
  }
}

/**
 * Get a map that maps column names to custom field id
 * of custom fields for given table
 *
 * @return array
 *  Array that has the column names of the custom fields
 *  as keys and the matching custom field id as value.
 */
function sepacontributionautofill_get_custom_fields_from_table($table) {
  $result = array();

  // Get columns of custom fields that are banking data fields.
  $custom_info = civicrm_api3('CustomField', 'get', array(
    'sequential' => 1,
    'return' => 'id,column_name',
    'custom_group_id' => sepacontributionautofill_get_group_id_from_table($table),
  ));

  foreach ($custom_info['values'] as $field) {
    $result[$field['column_name']] = "custom_{$field['id']}";
  }

  return $result;
}

/**
 * Get the id of an custom group by its table.
 */
function sepacontributionautofill_get_group_id_from_table($table) {
  // Find sepa custom field group.
  $sepa_group = civicrm_api3('CustomGroup', 'getsingle', array(
     'sequential' => 1,
     'return' => "id",
     'table_name' => $table,
  ));
  if (!is_array($sepa_group) || empty($sepa_group['id'])) {
    throw new Exception('Could not find custom sepa group.');
  }
  
  return $sepa_group['id'];
}

/**
 * Implementation of hook_civicrm_config
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_config
 */
function sepacontributionautofill_civicrm_config(&$config) {
  _sepacontributionautofill_civix_civicrm_config($config);
}

/**
 * Implementation of hook_civicrm_xmlMenu
 *
 * @param $files array(string)
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_xmlMenu
 */
function sepacontributionautofill_civicrm_xmlMenu(&$files) {
  _sepacontributionautofill_civix_civicrm_xmlMenu($files);
}

/**
 * Implementation of hook_civicrm_install
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_install
 */
function sepacontributionautofill_civicrm_install() {
  _sepacontributionautofill_civix_civicrm_install();
}

/**
 * Implementation of hook_civicrm_uninstall
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_uninstall
 */
function sepacontributionautofill_civicrm_uninstall() {
  _sepacontributionautofill_civix_civicrm_uninstall();
}

/**
 * Implementation of hook_civicrm_enable
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_enable
 */
function sepacontributionautofill_civicrm_enable() {
  _sepacontributionautofill_civix_civicrm_enable();
}

/**
 * Implementation of hook_civicrm_disable
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_disable
 */
function sepacontributionautofill_civicrm_disable() {
  _sepacontributionautofill_civix_civicrm_disable();
}

/**
 * Implementation of hook_civicrm_upgrade
 *
 * @param $op string, the type of operation being performed; 'check' or 'enqueue'
 * @param $queue CRM_Queue_Queue, (for 'enqueue') the modifiable list of pending up upgrade tasks
 *
 * @return mixed  based on op. for 'check', returns array(boolean) (TRUE if upgrades are pending)
 *                for 'enqueue', returns void
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_upgrade
 */
function sepacontributionautofill_civicrm_upgrade($op, CRM_Queue_Queue $queue = NULL) {
  return _sepacontributionautofill_civix_civicrm_upgrade($op, $queue);
}

/**
 * Implementation of hook_civicrm_managed
 *
 * Generate a list of entities to create/deactivate/delete when this module
 * is installed, disabled, uninstalled.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_managed
 */
function sepacontributionautofill_civicrm_managed(&$entities) {
  _sepacontributionautofill_civix_civicrm_managed($entities);
}

/**
 * Implementation of hook_civicrm_caseTypes
 *
 * Generate a list of case-types
 *
 * Note: This hook only runs in CiviCRM 4.4+.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_caseTypes
 */
function sepacontributionautofill_civicrm_caseTypes(&$caseTypes) {
  _sepacontributionautofill_civix_civicrm_caseTypes($caseTypes);
}

/**
 * Implementation of hook_civicrm_alterSettingsFolders
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_alterSettingsFolders
 */
function sepacontributionautofill_civicrm_alterSettingsFolders(&$metaDataFolders = NULL) {
  _sepacontributionautofill_civix_civicrm_alterSettingsFolders($metaDataFolders);
}
