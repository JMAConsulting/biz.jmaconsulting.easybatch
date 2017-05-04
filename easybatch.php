<?php

require_once 'easybatch.civix.php';

/**
 * Implementation of hook_civicrm_config
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_config
 */
function easybatch_civicrm_config(&$config) {
  _easybatch_civix_civicrm_config($config);
}

/**
 * Implementation of hook_civicrm_xmlMenu
 *
 * @param $files array(string)
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_xmlMenu
 */
function easybatch_civicrm_xmlMenu(&$files) {
  _easybatch_civix_civicrm_xmlMenu($files);
}

/**
 * Implementation of hook_civicrm_install
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_install
 */
function easybatch_civicrm_install() {
  _easybatch_civix_civicrm_install();
}

/**
 * Implementation of hook_civicrm_uninstall
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_uninstall
 */
function easybatch_civicrm_uninstall() {
  _easybatch_civix_civicrm_uninstall();
}

/**
 * Implementation of hook_civicrm_enable
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_enable
 */
function easybatch_civicrm_enable() {
  _easybatch_civix_civicrm_enable();
}

/**
 * Implementation of hook_civicrm_disable
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_disable
 */
function easybatch_civicrm_disable() {
  _easybatch_civix_civicrm_disable();
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
function easybatch_civicrm_upgrade($op, CRM_Queue_Queue $queue = NULL) {
  return _easybatch_civix_civicrm_upgrade($op, $queue);
}

/**
 * Implementation of hook_civicrm_managed
 *
 * Generate a list of entities to create/deactivate/delete when this module
 * is installed, disabled, uninstalled.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_managed
 */
function easybatch_civicrm_managed(&$entities) {
  _easybatch_civix_civicrm_managed($entities);
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
function easybatch_civicrm_caseTypes(&$caseTypes) {
  _easybatch_civix_civicrm_caseTypes($caseTypes);
}

/**
 * Implementation of hook_civicrm_alterSettingsFolders
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_alterSettingsFolders
 */
function easybatch_civicrm_alterSettingsFolders(&$metaDataFolders = NULL) {
  _easybatch_civix_civicrm_alterSettingsFolders($metaDataFolders);
}

/**
 * Implements hook_civicrm_preProcess().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_preProcess
 *
 */
function easybatch_civicrm_preProcess($formName, &$form) {
  if ($formName == 'CRM_Admin_Form_Preferences_Contribute') {
    $settings = $form->getVar('_settings');
    $contributeSettings = array();
    foreach ($settings as $key => $setting) {
      $contributeSettings[$key] = $setting;
      if ($key == 'default_invoice_page') {
        $contributeSettings['display_financial_batch'] = CRM_Core_BAO_Setting::CONTRIBUTE_PREFERENCES_NAME;
        $contributeSettings['require_financial_batch'] = CRM_Core_BAO_Setting::LOCALIZATION_PREFERENCES_NAME;
        $contributeSettings['auto_financial_batch'] = CRM_Core_BAO_Setting::CONTRIBUTE_PREFERENCES_NAME;
        $contributeSettings['batch_close_time'] = CRM_Core_BAO_Setting::CONTRIBUTE_PREFERENCES_NAME;
      }
    }
    $form->setVar('_settings', $contributeSettings);
  }
}

/**
 * Implements hook_civicrm_alterSettingsMetaData().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_alterSettingsMetaData
 *
 */
function easybatch_civicrm_alterSettingsMetaData(&$settingsMetadata, $domainID, $profile) {
  $settingsMetadata['display_financial_batch'] = array(
    'group_name' => 'Contribute Preferences',
    'group' => 'contribute',
    'name' => 'display_financial_batch',
    'type' => 'Integer',
    'html_type' => 'checkbox',
    'quick_form_type' => 'Element',
    'default' => 0,
    'add' => '4.7',
    'title' => 'Display Financial Batch of Backoffice forms?',
    'is_domain' => 1,
    'is_contact' => 0,
    'description' => '',
    'help_text' => '',
  );
  $settingsMetadata['require_financial_batch'] = array(
    'group_name' => 'Contribute Preferences',
    'group' => 'contribute',
    'name' => 'require_financial_batch',
    'type' => 'Integer',
    'html_type' => 'checkbox',
    'quick_form_type' => 'Element',
    'default' => 0,
    'add' => '4.7',
    'title' => 'Require Financial Batch of Backoffice forms?',
    'is_domain' => 1,
    'is_contact' => 0,
    'description' => '',
    'help_text' => '',
  );
  $settingsMetadata['auto_financial_batch'] = array(
    'group_name' => 'Contribute Preferences',
    'group' => 'contribute',
    'name' => 'auto_financial_batch',
    'type' => 'Integer',
    'html_type' => 'checkbox',
    'quick_form_type' => 'Element',
    'default' => 0,
    'add' => '4.7',
    'title' => 'Create automatic daily financial batches for Accounts Receivable frontend transactions?',
    'is_domain' => 1,
    'is_contact' => 0,
    'description' => '',
    'help_text' => '',
  );
  $settingsMetadata['batch_close_time'] = array(
    'group_name' => 'Contribute Preferences',
    'group' => 'contribute',
    'name' => 'batch_close_time',
    'type' => 'activityDateTime',
    'html_type' => 'Date',
    'quick_form_type' => 'Date',
    'default' => 0,
    'add' => '4.7',
    'title' => 'Automatic daily batch close time for frontend Accounts Receivable transactions',
    'is_domain' => 1,
    'is_contact' => 0,
    'description' => '',
    'help_text' => '',
  );
}

/**
 * Implements hook_civicrm_buildForm().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_buildForm
 *
 */
function easybatch_civicrm_buildForm($formName, &$form) {

  // Batch create form.
  if ($formName == 'CRM_Financial_Form_FinancialBatch') {
    $form->addEntityRef('contact_id', ts('Owner'), array(
      'create' => TRUE,
      'api' => array('extra' => array('email')),
    ), TRUE);
    $form->addEntityRef('org_id', ts('Company'), array(
      'create' => FALSE,
      'api' => array(
        'params' => array('contact_type' => 'Organization'),
      ),
    ));
    $form->addDate('batch_date', ts('Date'), FALSE, array('formatType' => 'activityDate'));
    CRM_Core_Region::instance('page-body')->add(array(
      'template' => 'CRM/EasyBatch/Form/ContactRef.tpl',
    ));
  }

  // Batch search form.
  if ($formName == 'CRM_Financial_Form_Search') {
    $form->addEntityRef('org_id', ts('Company'), array(
      'create' => FALSE,
      'api' => array(
        'params' => array('contact_type' => 'Organization'),
      ),
    ));
    $form->addDate('batch_date', ts('Date'), FALSE, array('formatType' => 'activityDate'));
    CRM_Core_Region::instance('page-body')->add(array(
      'template' => 'CRM/EasyBatch/Form/ContactRef.tpl',
    ));
  }

  // Contribution preferences form.
  if ($formName == 'CRM_Admin_Form_Preferences_Contribute') {

    // Create the select widgets for frontend forms.
    $batches = CRM_EasyBatch_BAO_EasyBatch::getEasyBatches();
    $isOrg = FALSE;
    if (count($batches) > 1) {
      $isOrg = TRUE;
    }
    foreach ($batches as $id => $batch) {
      $label = "Current automatic daily financial batch for A/R";
      if ($isOrg) {
        $label .= " - " . CRM_Contact_BAO_Contact::displayName($id);
      }
      $form->add('select', "auto_batch_{$id}", ts($label),
        array('' => '- ' . ts('select') . ' -') + $batch,
        FALSE
      );
    }

    // Assign the elements to the template
    $batches = array_combine(array_map(function($k){ return 'auto_batch_'.$k; }, array_keys($batches)), $batches);
    $form->assign('batchIDs', array_keys($batches));
    $form->assign('batchCount', count($batches));
    CRM_Core_Region::instance('page-body')->add(array(
      'template' => 'CRM/EasyBatch/Form/Admin.tpl',
    ));
  }

  // Add batch list selector.
  if (in_array($formName, array("CRM_Contribute_Form_Contribution", "CRM_Member_Form_Membership", "CRM_Event_Form_Participant", "CRM_Contribute_Form_AdditionalPayment"))) {
    if ($formName == 'CRM_Contribute_Form_AdditionalPayment' && $form->getVar('_view') == 'transaction' && ($form->_action & CRM_Core_Action::BROWSE)) {
      return FALSE;
    }
    if (Civi::settings()->get('display_financial_batch')) {
      $batches = array();
      $isRequired = FALSE;
      $result = civicrm_api3('Batch', 'get', array(
        'sequential' => 1,
        'return' => array("title"),
        'status_id' => "Open",
      ));
      if ($result['count'] > 0) {
        foreach ($result['values'] as $batch) {
          $batches[$batch['id']] = $batch['title'];
        }
      }
      if (Civi::settings()->get('require_financial_batch')) {
        $isRequired = TRUE;
      }

      // Add financial batch selector.
      $form->add('select', 'financial_batch_id', ts('Financial Batch'),
        array('' => '- ' . ts('select') . ' -') + $batches,
        $isRequired
      );

      // Set default batch if only one is present.
      if ($result['count'] == 1) {
        $form->setDefaults(array('financial_batch_id' => $result['id']));
      }
      CRM_Core_Region::instance('page-body')->add(array(
        'template' => 'CRM/EasyBatch/Form/FinancialBatch.tpl',
      ));
    }
  }

  // Add settings to payment processors.
  if ($formName == "CRM_Admin_Form_PaymentProcessor") {
    //$form->add('checkbox', 'pp_auto_financial_batch', ts('Create automatic daily financial batches?'));
  }
      
}

/**
 * Implements hook_civicrm_validateForm().
 *
 * @param string $formName
 * @param array $fields
 * @param array $files
 * @param CRM_Core_Form $form
 * @param array $errors
 */
function easybatch_civicrm_validateForm($formName, &$fields, &$files, &$form, &$errors) {
  if ($formName == "CRM_Admin_Form_Preferences_Contribute") {
    if (CRM_Utils_Array::value('require_financial_batch', $fields) && !CRM_Utils_Array::value('display_financial_batch', $fields)) {
      $errors['require_financial_batch'] = ts("Require financial batch on backoffice forms cannot be enabled unless display financial batch is also enabled.");
    }
  }
  if (in_array($formName, array("CRM_Contribute_Form_Contribution", "CRM_Member_Form_Membership", "CRM_Event_Form_Participant"))) {
    if (Civi::settings()->get('require_financial_batch') && !CRM_Utils_Array::value('financial_batch_id', $fields)) {
      $errors['financial_batch_id'] = ts("Select an open Financial Batch as required. Create one if necessary before creating contribution.");
    }
  }
}

/**
 * Implements hook_civicrm_postProcess().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_postProcess
 *
 */
function easybatch_civicrm_postProcess($formName, &$form) {
  // Backoffice forms.
  if (in_array($formName, array("CRM_Contribute_Form_Contribution", "CRM_Member_Form_Membership", "CRM_Event_Form_Participant", "CRM_Contribute_Form_AdditionalPayment"))) {
    if ($batchId = CRM_Utils_Array::value('financial_batch_id', $form->_submitValues)) {
      if ($formName == "CRM_Member_Form_Membership") {
        $result = civicrm_api3('MembershipPayment', 'get', array(
          'sequential' => 1,
          'return' => array("contribution_id"),
          'membership_id' => $form->_id,
        ));
        if ($result['count'] > 0) {
          $contributionId = $result['values'][0]['contribution_id'];
        }
      }
      elseif ($formName == "CRM_Event_Form_Participant") {
        $result = civicrm_api3('Participant', 'get', array(
          'sequential' => 1,
          'return' => array("id"),
          'contact_id' => $form->_contactId,
          'event_id' => 1,
          'api.ParticipantPayment.get' => array('participant_id' => "\$value.id"),
        ));
        if ($result['count'] > 0) {
          $contributionId = $result['values'][0]['api.ParticipantPayment.get']['values'][0]['contribution_id'];
        }
      }
      else {
        $contributionId = $form->_id;
      }
      CRM_EasyBatch_BAO_EasyBatch::addToBatch($batchId, $contributionId);
    }
  }

  // Frontend forms.
  if (in_array($formName, array("CRM_Contribute_Form_Contribution_Confirm", "CRM_Event_Form_Registration_Confirm"))) {
    if (!Civi::settings()->get('auto_financial_batch')) {
      return;
    }
    if ($formName == "CRM_Contribute_Form_Contribution_Confirm") {
      $financialTypeId = $form->_values['financial_type_id'];
      $contributionId = $form->_contributionID;
    }
    elseif ($formName == "CRM_Event_Form_Registration_Confirm") {
      $financialTypeId = $form->_values['event']['financial_type_id'];
      $contributionId = $form->_values['contributionId'];
    }
    $result = civicrm_api3('EntityFinancialAccount', 'get', array(
      'sequential' => 1,
      'return' => array("financial_account_id.id", "financial_account_id.name", "financial_account_id.contact_id"),
      'entity_table' => "civicrm_financial_type",
      'account_relationship' => "Accounts Receivable Account is",
      'entity_id' => $financialTypeId,
    ));
    if ($result['values'] > 0) {
      $batchId = CRM_Core_BAO_Setting::getItem(
        CRM_Core_BAO_Setting::CONTRIBUTE_PREFERENCES_NAME,
        'auto_batch_' . $result['values'][0]['financial_account_id.contact_id'],
        CRM_Core_Component::getComponentID('CiviContribute'),
        NULL,
        $result['values'][0]['financial_account_id.contact_id']
      );
      if (!empty($batchId)) {
        CRM_EasyBatch_BAO_EasyBatch::addToBatch($batchId, $contributionId);
      }
      else {
        // Create new batch if owner is not assigned to any open batch.
        CRM_EasyBatch_BAO_EasyBatch::openBatch($result['values'][0], $contributionId);
      }
    }
  }

  // Batch form.
  if ($formName == "CRM_Financial_Form_FinancialBatch") {
    $batchDate = CRM_Utils_Array::value('batch_date', $form->_submitValues, NULL);
    $params = array(
      'org_id' => CRM_Utils_Array::value('org_id', $form->_submitValues, NULL),
      'batch_date' => CRM_Utils_Date::processDate($batchDate),
    );
    $contactId = CRM_Utils_Array::value('contact_id', $form->_submitValues, NULL);
    $result = civicrm_api3('Batch', 'get', array(
      'sequential' => 1,
      'return' => array("id"),
      'title' => $form->_submitValues['title'],
    ));
    $batchId = $result['values'][0]['id'];
    if ($batchId && $contactId) {
      CRM_EasyBatch_BAO_EasyBatch::createEntityEasyBatch($batchId, $contactId, $params);
    }
  }

  // Component settings form.
  if ($formName == 'CRM_Admin_Form_Preferences_Contribute') {
    // Save the individual settings.
    $params = $form->_submitValues;
    $easyBatchParams = array(
      'display_financial_batch',
      'require_financial_batch',
      'auto_financial_batch',
      'batch_close_time_time',
    );
    foreach ($easyBatchParams as $field) {
      if (!empty($params[$field])) {
        Civi::settings()->set($field, $params[$field]);
      }
      else {
        Civi::settings()->set($field, 0);
      }
    }

    // Create batches if automatic daily batches is enabled.
    if (CRM_Utils_Array::value('auto_financial_batch', $params)) {
      // Only save the automatic batches the first time. After this, we will be generating the automatic batches via a scheduled job using the batch closing time.
      $count = CRM_Core_DAO::singleValueQuery("SELECT COUNT(id) FROM civicrm_easybatch_entity");
      if (!$count) {
        CRM_EasyBatch_BAO_EasyBatch::createFinancialBatchForAR();
      }
    }
    foreach ($params as $key => $value) {
      if (strpos($key, 'auto_batch_') !== FALSE) {
        // Create the settings for individual organizations.
        $contactID = substr(strrchr($key, "_"), 1);
        CRM_Core_BAO_Setting::setItem(
          $value,
          CRM_Core_BAO_Setting::CONTRIBUTE_PREFERENCES_NAME,
          $key,
          CRM_Core_Component::getComponentID('CiviContribute'),
          $contactID
        );
      }
    }
  }
}