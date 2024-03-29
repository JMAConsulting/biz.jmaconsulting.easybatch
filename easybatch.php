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
 * Implementation of hook_civicrm_install
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_install
 */
function easybatch_civicrm_install() {
  _easybatch_civix_civicrm_install();
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
   if (CRM_Core_Permission::check('edit all manual batches')) {
      $form->addEntityRef('created_id', ts('Owner'), array(
        'create' => TRUE,
        'api' => array('extra' => array('email')),
      ), TRUE);
    }
    $form->add('select', 'org_id', ts('Organization'),
      CRM_Financial_BAO_FinancialAccount::getOrganizationNames(FALSE),
      FALSE, array('class' => 'crm-select2', 'placeholder' => ts('- any -'))
    );

    $form->addDate('batch_date', ts('Batch Date'), FALSE, array('formatType' => 'activityDate'));
    CRM_Core_Region::instance('page-body')->add(array(
      'template' => 'CRM/EasyBatch/Form/ContactRef.tpl',
    ));
    $batchId = $form->getVar('_id');
    if (!$batchId) {
      $defaults = array('created_id' => CRM_Core_Session::singleton()->get('userID'));
      // Default org ID.
      $orgId = CRM_Core_DAO::singleValueQuery("SELECT contact_id FROM civicrm_financial_account WHERE is_active = 1 ORDER by id LIMIT 1");
      if ($orgId) {
        $defaults['org_id'] = $orgId;
      }
      list($defaults['batch_date'], $defaults['batch_date_time']) = CRM_Utils_Date::setDateDefaults(date('Ymd'), 'activityDate');
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
    $form->addEntityRef('org_id', ts('Organization'), array(
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
          //"CRM_Contribute_Form_Contribution",
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
        $form->setDefaults(array('financial_batch_id' => key($batches)));
      }
      if ($form->getVar('_action') & CRM_Core_Action::UPDATE) {
        $batchId = CRM_EasyBatch_BAO_EasyBatch::getBatchIDForContribution($form->getVar('_id'));
        $form->setDefaults(array('financial_batch_id' => $batchId));
      }

      $nonPaymentStatuses = array();
      foreach(CRM_Contribute_PseudoConstant::contributionStatus(NULL, 'name') as $statusID => $name) {
        if (in_array($name, array(
          'Cancelled',
          'Failed',
          'Pending Refund',
          'Refunded',
          'Pending',
        ))) {
          $nonPaymentStatuses[] = $statusID;
        }
      }
      $form->assign('statuses', json_encode($nonPaymentStatuses));

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
    $form->add('checkbox', 'auto_financial_batch', ts('Create Automatic Daily Financial Batches for Payments?'));
    $form->add('checkbox', 'cc_financial_batch', ts('Create separate batches per card type?'));
    $form->addDate('batch_close_time', ts('Automatic Daily Batch Close Time'), FALSE, array('formatType' => 'activityDateTime'));
    $paymentProcessorId = $form->getVar('_id');
    $batches = CRM_EasyBatch_BAO_EasyBatch::getEasyBatches($paymentProcessorId);
    $form->assign('batches', $batches);
    $form->assign('isHideBatch', FALSE);
    if ($paymentProcessorId
      && Civi::settings()->get("pp_auto_financial_batch_{$paymentProcessorId}")
    ) {
      $defaults = array(
        'auto_financial_batch' => Civi::settings()->get("pp_auto_financial_batch_{$paymentProcessorId}"),
        'cc_financial_batch' => Civi::settings()->get("pp_cc_financial_batch_{$paymentProcessorId}"),
        'batch_close_time_time' => Civi::settings()->get("pp_batch_close_time_{$paymentProcessorId}"),
      );
      $form->setDefaults($defaults);
      if (!empty($defaults['auto_financial_batch'])) {
        $form->assign('isHideBatch', TRUE);
      }
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
    "CRM_Event_Form_ParticipantFeeSelection",
    "CRM_Contribute_Form_ContributionPage_Amount",
    "CRM_Event_Form_ManageEvent_Fee",
  ))) {
    if ($form->getVar('_action') & CRM_Core_Action::DELETE) {
      return FALSE;
    }
    if (in_array($formName, array(
        "CRM_Member_Form_Membership",
        "CRM_Event_Form_Participant",
        "CRM_Member_Form_MembershipRenewal",
      ))
      && !CRM_Utils_Array::value('record_contribution', $fields) && !$form->_mode
    ) {
      return FALSE;
    }
    $fieldName = 'payment_instrument_id';
    if (in_array($formName, array('CRM_Event_Form_ParticipantFeeSelection', 'CRM_Contribute_Form_ContributionPage_Amount'))) {
      $fieldName = '_qf_default';
    }
    if ($formName == 'CRM_Contribute_Form_ContributionPage_Amount') {
      $fields['financial_type_id'] = CRM_Utils_Array::value('financial_type_id', $form->getVar('_values'));
      if (!empty($fields['payment_processor']) && isset($fields['payment_processor'])) {
        $fields['payment_processor_id'] = array_keys($fields['payment_processor']);
      }
    }
    elseif ($formName == 'CRM_Event_Form_ManageEvent_Fee' && !empty($fields['payment_processor'])) {
      $fields['payment_processor_id'] = explode(',', $fields['payment_processor']);
    }

    if (in_array($formName, array(
      'CRM_Contribute_Form_AdditionalPayment',
      'CRM_Event_Form_ParticipantFeeSelection'))
    ) {
      $fields['financial_type_id'] = CRM_Core_DAO::getFieldValue('CRM_Contribute_DAO_Contribution', $form->getVar('_contributionId'), 'financial_type_id');
    }
    try {
      CRM_EasyBatch_BAO_EasyBatch::checkFTWithSameOrg($form, $fields);
    } catch (CRM_Core_Exception $e) {
      $errors[$fieldName] = $e->getMessage();
    }
    if ($formName == 'CRM_Event_Form_ParticipantFeeSelection') {
      return NULL;
    }
    $financialEasyBatchId = FALSE;
    // Backoffice forms.
    if (in_array($formName, array(
      "CRM_Contribute_Form_Contribution",
      "CRM_Member_Form_Membership",
      "CRM_Event_Form_Participant",
      "CRM_Contribute_Form_AdditionalPayment",
      "CRM_Member_Form_MembershipRenewal",
    ))) {
      $financialEasyBatchId = TRUE;
    }
    if ($financialEasyBatchId && Civi::settings()->get('require_financial_batch') && !CRM_Utils_Array::value('financial_batch_id', $fields)) {
      $errors['financial_batch_id'] = ts("Select an open Financial Batch as required. Create one if necessary before creating contribution.");
    }
    if ($formName == "CRM_Contribute_Form_Contribution" && (CRM_Utils_Array::value('contribution_status_id', $fields) == 3)) {
      unset($errors['financial_batch_id']);
    }
    if ($financialEasyBatchId && !empty($fields['financial_batch_id'])) {
      if (CRM_EasyBatch_BAO_EasyBatch::checkBatchWithSameOrg($fields['financial_batch_id'], $fields)) {
        $errors['financial_batch_id'] = ts("The Payment Method/Payment Processor and Financial Batch should be associated with the same organization.");
      }
    }
    if (!empty($fields['payment_instrument_id']) && !empty($fields['credit_note_id'])) {
      try {
        CRM_EasyBatch_BAO_EasyBatch::checkCreditNote($fields['payment_instrument_id'], $fields['credit_note_id']);
      } catch (CRM_Core_Exception $e) {
        $errors[$fieldName] = $e->getMessage();
      }
    }
  }
  if ('CRM_Admin_Form_PaymentProcessor' == $formName) {
    if (CRM_EasyBatch_BAO_EasyBatch::checkPaymentProcessorOwner($fields['financial_account_id'], $fields['payment_instrument_id'])) {
      $errors['financial_account_id'] = ts("The Payment Method and Financial Account should be associated with the same organization.");
    }
  }
  if ($formName == "CRM_Financial_Form_FinancialBatch") {
    if (($form->_action & CRM_Core_Action::UPDATE) && $form->_defaultValues['org_id'] != CRM_Utils_Array::value('org_id', $fields)) {
      try {
        CRM_EasyBatch_BAO_EasyBatch::checkTransactions($form->getVar('_id'), $fields['org_id']);
      } catch (CRM_Core_Exception $e) {
        $errors['org_id'] = $e->getMessage();
      }
    }
    if (!empty($fields['org_id']) && !empty($fields['payment_instrument_id'])) {
      $paymentMethodOwnerID = CRM_EasyBatch_BAO_EasyBatch::getPaymentMethodOwnerID($fields['payment_instrument_id']);
      if ($paymentMethodOwnerID != $fields['org_id']) {
         $orgName = CRM_Contact_BAO_Contact::displayName($fields['org_id']);
         $errors['payment_instrument_id'] = ts("The Payment Method should be associated with the '{$orgName}' organization.");
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
      "pp_cc_financial_batch_{$paymentProcessorId}",
      CRM_Utils_Array::value('cc_financial_batch', $submitValues)
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
    $instanceID = CRM_Report_Utils_Report::getInstanceIDForValue('biz.jmaconsulting.easybatch/batchdetail');
    $url = CRM_Report_Utils_Report::getNextUrl(
      'biz.jmaconsulting.easybatch/batchdetail',
      'reset=1&force=1&id_op=eq&id_value=' . $objectId,
      TRUE,
      $instanceID
    );
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
    $links[] = array(
      'name' => 'Batch Details Report',
      'url' => $url,
      'title' => 'Batch Details Report',
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
