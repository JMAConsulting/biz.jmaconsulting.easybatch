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
      if ($key == 'acl_financial_type') {
        $contributeSettings['display_financial_batch'] = CRM_Core_BAO_Setting::CONTRIBUTE_PREFERENCES_NAME;
        $contributeSettings['require_financial_batch'] = CRM_Core_BAO_Setting::CONTRIBUTE_PREFERENCES_NAME;
      }
      if ($key == 'always_post_to_accounts_receivable') {
        $contributeSettings['auto_batch_non_payment_trxns'] = CRM_Core_BAO_Setting::CONTRIBUTE_PREFERENCES_NAME;
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
    'title' => 'Display Financial Batch on payments through Backoffice forms?',
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
    'title' => 'Require Financial Batch on payments through Backoffice forms?',
    'is_domain' => 1,
    'is_contact' => 0,
    'description' => '',
    'help_text' => '',
  );
  $settingsMetadata['auto_batch_non_payment_trxns'] = array(
    'group_name' => 'Contribute Preferences',
    'group' => 'contribute',
    'name' => 'auto_batch_non_payment_trxns',
    'type' => 'Integer',
    'html_type' => 'select',
    'quick_form_type' => 'Element',
    'default' => 0,
    'option_values' => array(
      0 => ts('No'),
      'IIF' => ts('Into .iif file'),
      'CSV' => ts('Into .csv file'),
    ),
    'add' => '4.7',
    'title' => 'Automatically batch non-payment transactions?',
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
  if ('CRM_Financial_Form_Export' == $formName) {
    $batchId = $form->getVar('_id');
    $isRedirect = FALSE;
    if ($batchId) {
      if (CRM_EasyBatch_BAO_EasyBatch::isOpenAutoBatch($batchId)) {
        $isRedirect = TRUE;
      }
    }
    else {
      $batchIds = explode(',', $form->getVar('_batchIds'));
      $isAutoOpenBatch = FALSE;
      foreach ($batchIds as $key => $batchId) {
        if (CRM_EasyBatch_BAO_EasyBatch::isOpenAutoBatch($batchId)) {
          unset($batchIds[$key]);
          $isAutoOpenBatch = TRUE;
        }
      }
      $form->setVar('_batchIds', implode(',', $batchIds));
      $batchNames = array();
      if ($batchIds) {
        $batchNames = CRM_Batch_BAO_Batch::getBatchNames($batchIds);
      }
      $form->assign('batchNames', $batchNames);
      if ($isAutoOpenBatch) {
        if (!$batchIds) {
          $isRedirect = TRUE;
        }
        else {
          CRM_Core_Session::setStatus(ts('Some batches cannot be exported since they are used as auto batch.'), ts('Batch Update'), 'warning');
        }
      }
    }
    if ($isRedirect) {
      $openBatchStatusId = CRM_Core_PseudoConstant::getKey(
        'CRM_Batch_BAO_Batch',
        'status_id',
        'Open'
       );
      CRM_Utils_System::redirect(CRM_Utils_System::url('civicrm/financial/financialbatches', "reset=1&batchStatus={$openBatchStatusId}"));
    }
  }

  // Batch create form.
  if ($formName == 'CRM_Financial_Form_FinancialBatch') {
    $form->addEntityRef('created_id', ts('Owner'), array(
      'create' => TRUE,
      'api' => array('extra' => array('email')),
    ), TRUE);

    $form->add('select', 'org_id', ts('Company'),
      CRM_Financial_BAO_FinancialAccount::getOrganizationNames(),
      FALSE, array('class' => 'crm-select2', 'placeholder' => ts('- any -'))
    );

    $form->addDate('batch_date', ts('Batch Date'), FALSE, array('formatType' => 'activityDate'));
    CRM_Core_Region::instance('page-body')->add(array(
      'template' => 'CRM/EasyBatch/Form/ContactRef.tpl',
    ));
    $batchId = $form->getVar('_id');
    if (!$batchId) {
      $defaults = array('created_id' => CRM_Core_Session::singleton()->get('userID'));
    }
    else {
      $values = CRM_EasyBatch_BAO_EasyBatch::retrieve(array('batch_id' => $batchId));
      $defaults = array(
        'org_id' => CRM_Utils_Array::value('contact_id', $values),
      );
      if (!empty($values['batch_date'])) {
        list($defaults['batch_date'], $defaults['batch_date_time']) = CRM_Utils_Date::setDateDefaults($values['batch_date'], 'activityDate');
      }
    }
    $form->setDefaults($defaults);
  }

  // Batch search form.
  if ($formName == 'CRM_Financial_Form_Search') {
    $form->addEntityRef('org_id', ts('Company'), array(
      'create' => FALSE,
      'api' => array(
        'params' => array('contact_type' => 'Organization'),
      ),
    ));
    $form->add('text', 'batch_date_from', ts('Batch Date from'));
    $form->addDate('batch_date_from_hidden', ts('Date'), FALSE, array('formatType' => 'activityDate'));
    $form->add('text', 'batch_date_to', ts('Batch Date to'));
    $form->addDate('batch_date_to_hidden', ts('Date'), FALSE, array('formatType' => 'activityDate'));
    $updatedElements = array();
    $elements = $form->get_template_vars('elements');
    foreach ($elements as $element) {
      $updatedElements[] = $element;
      if ($element == 'status_id') {
        $updatedElements[] = 'batch_date_from';
        $updatedElements[] = 'batch_date_to';
        $updatedElements[] = 'org_id';
      }
    }
    $form->assign('elements', $updatedElements);
    CRM_Core_Region::instance('page-body')->add(array(
      'template' => 'CRM/EasyBatch/Form/BatchSearch.tpl',
    ));
  }

  // Contribution preferences form.
  if ($formName == 'CRM_Admin_Form_Preferences_Contribute') {
    // Create the select widgets for frontend forms.
    $batches = CRM_EasyBatch_BAO_EasyBatch::getEasyBatches();
    $form->assign('batches', $batches);
    CRM_Core_Region::instance('page-body')->add(array(
      'template' => 'CRM/EasyBatch/Form/Admin.tpl',
    ));
  }

  // Add batch list selector.
  if (in_array($formName, array(
    "CRM_Contribute_Form_Contribution",
    "CRM_Member_Form_Membership",
    "CRM_Event_Form_Participant",
    "CRM_Contribute_Form_AdditionalPayment",
    "CRM_Member_Form_MembershipRenewal",
  ))) {
    if ($form->_mode) {
      return FALSE;
    }
    if ($form->getVar('_action') & CRM_Core_Action::DELETE) {
      return FALSE;
    }
    if ($formName == 'CRM_Contribute_Form_AdditionalPayment'
      && $form->getVar('_view') == 'transaction'
      && ($form->_action & CRM_Core_Action::BROWSE)
    ) {
      return FALSE;
    }

    if (Civi::settings()->get('display_financial_batch')) {
      $isRequired = FALSE;
      if (Civi::settings()->get('require_financial_batch')
        && in_array($formName, array(
          "CRM_Contribute_Form_Contribution",
          "CRM_Contribute_Form_AdditionalPayment"
        ))
      ) {
        $isRequired = TRUE;
      }

      // Add financial batch selector.
      $batches = CRM_EasyBatch_BAO_EasyBatch::getAllNonAutoBatches();
      $form->add('select', 'financial_batch_id', ts('Financial Batch'),
        array('' => '- ' . ts('select') . ' -') + $batches,
        $isRequired
      );

      // Set default batch if only one is present.
      if (count($batches) == 1) {
        $form->setDefaults(array('financial_batch_id' => reset($batches)));
      }
      if ($form->getVar('_action') & CRM_Core_Action::UPDATE) {
        $batchId = CRM_EasyBatch_BAO_EasyBatch::getBatchIDForContribution($form->getVar('_id'));
        $form->setDefaults(array('financial_batch_id' => $batchId));
      }
      CRM_Core_Region::instance('page-body')->add(array(
        'template' => 'CRM/EasyBatch/Form/FinancialBatch.tpl',
      ));
      if ($form->_flagSubmitted) {
        $form->assign('backendFormSubmit', TRUE);
        $batchId = CRM_Utils_Array::value('financial_batch_id', $form->_submitValues);
        if ($batchId) {
          $form->assign('financialEasyBatchId', $batchId);
        }
      }
    }
  }

  // Add settings to payment processors.
  if ($formName == "CRM_Admin_Form_PaymentProcessor") {
    $form->add('checkbox', 'auto_financial_batch', ts('Create Automatic Daily Financial Batches?'));
    $form->addDate('batch_close_time', ts('Automatic Daily Batch Close Time'), FALSE, array('formatType' => 'activityDateTime'));
    $paymentProcessorId = $form->getVar('_id');
    if ($paymentProcessorId
      && Civi::settings()->get("pp_auto_financial_batch_{$paymentProcessorId}")
    ) {
      $batches = CRM_EasyBatch_BAO_EasyBatch::getEasyBatches($paymentProcessorId);
      $form->assign('batches', $batches);
      $defaults = array(
        'auto_financial_batch' => Civi::settings()->get("pp_auto_financial_batch_{$paymentProcessorId}"),
        'batch_close_time_time' => Civi::settings()->get("pp_batch_close_time_{$paymentProcessorId}"),
      );
      $form->setDefaults($defaults);
    }
    CRM_Core_Region::instance('page-body')->add(array(
      'template' => 'CRM/Admin/Form/PaymentProcessorExtra.tpl',
    ));
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
  if (in_array($formName, array(
    "CRM_Contribute_Form_Contribution",
    "CRM_Member_Form_Membership",
    "CRM_Event_Form_Participant",
    "CRM_Contribute_Form_AdditionalPayment",
    "CRM_Member_Form_MembershipRenewal",
  ))) {
    if ($form->_mode) {
      return FALSE;
    }
    if ($form->getVar('_action') & CRM_Core_Action::DELETE) {
      return FALSE;
    }
    if (in_array($formName, array(
        "CRM_Member_Form_Membership",
        "CRM_Event_Form_Participant",
        "CRM_Member_Form_MembershipRenewal",
      ))
      && !CRM_Utils_Array::value('record_contribution', $fields)
    ) {
      return FALSE;
    }
    if (Civi::settings()->get('require_financial_batch') && !CRM_Utils_Array::value('financial_batch_id', $fields)) {
      $errors['financial_batch_id'] = ts("Select an open Financial Batch as required. Create one if necessary before creating contribution.");
    }
  }
  if ($formName == "CRM_Financial_Form_FinancialBatch" && ($form->_action & CRM_Core_Action::UPDATE)) {
    if ($form->_defaultValues['org_id'] != CRM_Utils_Array::value('org_id', $fields)) {
      if (CRM_EasyBatch_BAO_EasyBatch::checkTransactions($form->getVar('_id'))) {
        $errors['org_id'] = ts("Cannot change company since there are one or more transactions assigned to batch.");
      }
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
  if (in_array($formName, array(
    "CRM_Contribute_Form_Contribution",
    "CRM_Member_Form_Membership",
    "CRM_Event_Form_Participant",
    "CRM_Contribute_Form_AdditionalPayment",
    "CRM_Member_Form_MembershipRenewal",
  ))) {
    $form->assign('financialEasyBatchId', NULL);
    $form->assign('backendFormSubmit', NULL);
  }

  // Batch form.
  if ($formName == "CRM_Financial_Form_FinancialBatch") {
    $batchDate = CRM_Utils_Array::value('batch_date', $form->_submitValues, NULL);
    $params = array(
      'contact_id' => NULL,
      'batch_date' => CRM_Utils_Date::processDate($batchDate),
      'batch_id' => $form->getVar('_id'),
    );
    $id = CRM_Core_DAO::getFieldValue('CRM_EasyBatch_DAO_EasyBatchEntity', $form->getVar('_id'), 'id', 'batch_id');
    if ($id) {
      $params['id'] = $id;
    }
    if (!CRM_Utils_System::isNull($form->_submitValues['org_id'])) {
      $params['contact_id'] = $form->_submitValues['org_id'];
    }
    CRM_EasyBatch_BAO_EasyBatch::create($params);
  }

  // Component settings form.
  if ($formName == 'CRM_Admin_Form_Preferences_Contribute') {
    // Save the individual settings.
    $params = $form->_submitValues;
    $easyBatchParams = array(
      'display_financial_batch',
      'require_financial_batch',
      'auto_batch_non_payment_trxns',
    );
    foreach ($easyBatchParams as $field) {
      Civi::settings()->set($field, CRM_Utils_Array::value($field, $params, 0));
    }
    if (CRM_Utils_Array::value('auto_batch_non_payment_trxns', $params)) {
      CRM_EasyBatch_BAO_EasyBatch::createAutoNonPaymentFinancialBatch();
    }
  }

  if ($formName == 'CRM_Admin_Form_PaymentProcessor') {
    $submitValues = $form->_submitValues;
    $paymentProcessorId = $form->getVar('_id');
    if (!$paymentProcessorId) {
      $paymentProcessorId = civicrm_api3('PaymentProcessor', 'getSingle', array(
        'return' => array("id"),
        'name' => $submitValues['name'],
        'is_test' => 0,
      ));
      $paymentProcessorId = $paymentProcessorId['id'];
    }
    Civi::settings()->set(
      "pp_auto_financial_batch_{$paymentProcessorId}",
      CRM_Utils_Array::value('auto_financial_batch', $submitValues)
    );
    Civi::settings()->set(
      "pp_batch_close_time_{$paymentProcessorId}",
      CRM_Utils_Array::value('batch_close_time_time', $submitValues)
    );
    if (!empty($submitValues['auto_financial_batch'])) {
      CRM_EasyBatch_BAO_EasyBatch::createAutoFinancialBatch(
        $submitValues['financial_account_id'],
        $submitValues['name'],
        $paymentProcessorId
      );
    }
  }

}

/**
 * Implements hook_civicrm_post().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_post
 *
 */
function easybatch_civicrm_post($op, $objectName, $objectId, &$objectRef) {
  if ($objectName == 'Batch' && in_array($op, array('edit'))) {
    $action = CRM_Core_Smarty::singleton()->get_template_vars('batchAction');
    if ($action) {
      $msg = '';
      if ($op == 'edit') {
        //$objectRef = NULL;
        $msg = 'Auto open batch cannot be closed or exported.';
      }
      CRM_Core_Session::setStatus(ts($msg), ts('Batch Update'), 'error');
    }
  }

  if ($objectName == 'FinancialAccount' && in_array($op, array('create', 'edit'))) {
    $sql = "SELECT contact_id FROM civicrm_financial_account WHERE is_active = 1 GROUP BY contact_id";
    $dao = CRM_Core_DAO::executeQuery($sql);
    $suffix = NULL;
    if ($dao->N > 1) {
      $suffix = CRM_Contact_BAO_Contact::displayName($objectRef->contact_id);
    }
    CRM_EasyBatch_BAO_EasyBatch::createAutoFinancialBatch($objectId, $suffix);
  }
}

/**
 * Implements hook_civicrm_postSave_table_name().
 *
 * @link https://docs.civicrm.org/dev/en/master/hooks/hook_civicrm_postSave_table_name/
 *
 */
function easybatch_civicrm_postSave_civicrm_financial_trxn($dao) {
  $backendFormSubmit = CRM_Core_Smarty::singleton()->get_template_vars('backendFormSubmit');
  if ($backendFormSubmit && $dao->is_payment) {
    $financialEasyBatchId = CRM_Core_Smarty::singleton()->get_template_vars('financialEasyBatchId');
    if ($financialEasyBatchId) {
      CRM_EasyBatch_BAO_EasyBatch::addTransactionsToBatch($financialEasyBatchId, $dao->id);
    }
  }
  else {
    CRM_EasyBatch_BAO_EasyBatch::addTransactionsToAutoBatch($dao);
  }
}

/**
 * Implements hook_civicrm_entityTypes().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_entityTypes
 *
 */
function easybatch_civicrm_entityTypes(&$entityTypes) {
  $entityTypes[] = array(
    'name'  => 'EasyBatchEntity',
    'class' => 'CRM_EasyBatch_DAO_EasyBatchEntity',
    'table' => 'civicrm_easybatch_entity',
  );
}

/**
 * Implements hook_civicrm_pre().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_pre
 *
 */
function easybatch_civicrm_pre($op, $objectName, &$objectId, &$params) {
  if ($objectName == 'Batch' && in_array($op, array('edit', 'delete'))) {
    if (!CRM_EasyBatch_BAO_EasyBatch::isOpenAutoBatch($objectId)
      || CRM_Utils_Array::value('force_close', $params)
    ) {
      return FALSE;
    }
    if ($op == 'edit') {
      $openStatusID = CRM_Core_PseudoConstant::getKey('CRM_Batch_BAO_Batch', 'status_id', 'Open');
      $params['status_id'] = $openStatusID;
      CRM_Core_Smarty::singleton()->assign('batchAction', TRUE);
    }
    if ($op == 'delete') {
      throw new CRM_Core_Exception(ts("Auto open batch cannot be deleted."));
    }
  }
}

/**
 * Implements hook_civicrm_apiWrappers().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_apiWrappers
 *
 */
function easybatch_civicrm_apiWrappers(&$wrappers, $apiRequest) {;
  if ($apiRequest['entity'] == 'Batch' && $apiRequest['action'] == 'get') {
    $wrappers[] = new CRM_EasyBatch_BatchAPIWrapper();
  }
}

function easybatch_civicrm_links($op, $objectName, &$objectId, &$links, &$mask = NULL, &$values = array()) {
  if ($objectName == 'Batch' && 'batch.selector.row' == $op) {
    $easyBatches = CRM_Core_Smarty::singleton()->get_template_vars('easyBatch');
    $company = CRM_Utils_Array::value('org_id', CRM_Utils_Array::value($objectId, $easyBatches));
    $date = CRM_Utils_Array::value('batch_date', CRM_Utils_Array::value($objectId, $easyBatches));
    $dateFormat = Civi::settings()->get('dateformatFinancialBatch');
    $date =  CRM_Utils_Date::customFormat($date, $dateFormat);
    $links[] = array(
      'name' => '',
      'url' => '',
      'qs' => '',
      'title' => '',
      'ref' => " rowBatchData-{$objectId}",
      'extra' => " style='Display:none;' company ='{$company}' batchDate = '{$date}'",
    );
    if (!CRM_EasyBatch_BAO_EasyBatch::isOpenAutoBatch($objectId)) {
      return FALSE;
    }
    foreach ($links as $id => $link) {
      if (in_array(strtolower($link['name']), array('edit', 'export', 'close', 'delete'))) {
        unset($links[$id]);
      }
    }
  }
}
