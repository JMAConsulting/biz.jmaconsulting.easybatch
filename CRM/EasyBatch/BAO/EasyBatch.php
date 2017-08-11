<?php
/*
 +--------------------------------------------------------------------+
 | Close Accounting Period Extension                                  |
 +--------------------------------------------------------------------+
 | Copyright (C) 2016-2017 JMA Consulting                             |
 +--------------------------------------------------------------------+
 | This file is a part of CiviCRM.                                    |
 |                                                                    |
 | CiviCRM is free software; you can copy, modify, and distribute it  |
 | under the terms of the GNU Affero General Public License           |
 | Version 3, 19 November 2007 and the CiviCRM Licensing Exception.   |
 |                                                                    |
 | CiviCRM is distributed in the hope that it will be useful, but     |
 | WITHOUT ANY WARRANTY; without even the implied warranty of         |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.               |
 | See the GNU Affero General Public License for more details.        |
 |                                                                    |
 | You should have received a copy of the GNU Affero General Public   |
 | License and the CiviCRM Licensing Exception along                  |
 | with this program; if not, contact CiviCRM LLC                     |
 | at info[AT]civicrm[DOT]org. If you have questions about the        |
 | GNU Affero General Public License or the licensing of CiviCRM,     |
 | see the CiviCRM license FAQ at http://civicrm.org/licensing        |
 +--------------------------------------------------------------------+
 */

/**
 *
 * @package CRM
 * @copyright CiviCRM LLC (c) 2004-2017
 * $Id$
 *
 */
class CRM_EasyBatch_BAO_EasyBatch extends CRM_EasyBatch_DAO_EasyBatchEntity {

  static $_paymentMethodOwnerID = NULL;
  static $_paymentProcessorOwnerID = NULL;

  public function __construct() {
    parent::__construct();
  }

  public static function create($params) {
    $entity = new CRM_EasyBatch_DAO_EasyBatchEntity();
    $entity->copyValues($params);
    $entity->save();
    return $entity;
  }

  /**
   * Fetch object based on array of properties.
   *
   * @param array $params
   *   (reference ) an assoc array of name/value pairs.
   *
   * @return array
   */
  public static function retrieve($params) {
    $entityBatch = new CRM_EasyBatch_DAO_EasyBatchEntity();
    $entityBatch->copyValues($params);
    $values = array();
    if ($entityBatch->find(TRUE)) {
      CRM_Core_DAO::storeValues($entityBatch, $values);
    }
    return $values;
  }

  /**
   * Create entry in easybatch entity table.
   */
  public static function getEasyBatches(
    $paymentProcessorID = NULL,
    $returnColumn = 'title',
    $isAuto = TRUE
  ) {
    $status = CRM_Core_PseudoConstant::getKey('CRM_Batch_BAO_Batch', 'status_id', 'Open');
    $easyBatches = array();
    $where = array("b.status_id = {$status}");
    $where[] = "e.is_automatic = {$isAuto}";
    if ($paymentProcessorID) {
      $where[] = "e.payment_processor_id = {$paymentProcessorID}";
    }
    else {
      $where[] = "e.payment_processor_id IS NULL";
    }
    $sql = "SELECT b.id, {$returnColumn}
      FROM civicrm_batch b
      LEFT JOIN civicrm_easybatch_entity e ON b.id = e.batch_id
      WHERE " . implode(' AND ', $where);
    $dao = CRM_Core_DAO::executeQuery($sql);
    while ($dao->fetch()) {
      $easyBatches[$dao->id] = $dao->$returnColumn;
    }
    return $easyBatches;
  }

  /**
   * Check if transaction already added to batch.
   */
  public static function checkIfFTAlreadyAddedToBatch($trxnId) {
    $entityBatchCount = civicrm_api3('EntityBatch', 'getCount', array(
      'entity_table' => "civicrm_financial_trxn",
      'entity_id' => $trxnId,
    ));
    if ($entityBatchCount > 0) {
      return TRUE;
    }
    return FALSE;
  }

  /**
   * Check if batch is still open while trying to save a new contribution.
   */
  public static function checkIfBatchOpen($batchId) {
    $batch = civicrm_api3('Batch', 'getsingle', array(
      'return' => array("status_id"),
      'id' => $batchId,
    ));
    $openBatchStatusId = CRM_Core_PseudoConstant::getKey('CRM_Batch_BAO_Batch', 'status_id', 'Open');
    if ($batch['status_id'] == $openBatchStatusId) {
      return TRUE;
    }
    return FALSE;
  }

  /**
   * Add financial transaction entry to batch.
   */
  public static function addTransactionsToBatch($batchId, $financialTrxnId) {
    if (self::checkIfFTAlreadyAddedToBatch($financialTrxnId)) {
      return FALSE;
    }
    $tx = new CRM_Core_Transaction();

    civicrm_api3('EntityBatch', 'create', array(
      'entity_table' => "civicrm_financial_trxn",
      'entity_id' => $financialTrxnId,
      'batch_id' => $batchId,
    ));

    if (!self::checkIfBatchOpen($batchId)) {
      // FIXME: This should end up rolling back the entire contribution, not just the entity batch creation.
      $tx->rollback();
      return FALSE;
    }
    else {
      return TRUE;
    }
  }

  /**
   * Process Auto financial batch.
   */
  public static function addTransactionsToAutoBatch($financialTrxn) {
    $financialEasyBatchId = NULL;
    if ($financialTrxn->is_payment) {
      if ($financialTrxn->payment_processor_id
        && Civi::settings()->get("pp_auto_financial_batch_{$financialTrxn->payment_processor_id}")
      ) {
        $batches = CRM_EasyBatch_BAO_EasyBatch::getEasyBatches($financialTrxn->payment_processor_id);
        $financialEasyBatchId = key($batches);
      }
    }
    else {
      if (Civi::settings()->get("auto_batch_non_payment_trxns")) {
        $financialAccountId = empty($financialTrxn->from_financial_account_id) ? $financialTrxn->to_financial_account_id : $financialTrxn->from_financial_account_id;
        if (!$financialAccountId) return;
        $contactId = civicrm_api3('FinancialAccount', 'getSingle', array(
          'return' => array("contact_id"),
          'id' => $financialAccountId,
        ));
        $contactId = $contactId['contact_id'];
        $batches = self::getEasyBatches(NULL, 'contact_id');
        $financialEasyBatchId = array_search($contactId, $batches);
      }
    }

    if ($financialEasyBatchId) {
      self::addTransactionsToBatch($financialEasyBatchId, $financialTrxn->id);
    }

  }

  /**
   * Create entry in easybatch entity table.
   */
  public static function createEntityEasyBatch($batchId, $cid, $addParams = array()) {
    $params = array(
      'batch_id' => $batchId,
      'contact_id' => $cid,
    );
    if (!empty($addParams)) {
      $params = array_merge($params, $addParams);
    }
    $entity = self::create($params);
    return $entity;
  }

  /**
   * Create Auto Financial Batch
   */
  public static function createAutoFinancialBatch(
    $financialAccountId,
    $suffixName = NULL,
    $paymentProcessorID = NULL
  ) {
    $contactId = civicrm_api3('FinancialAccount', 'getSingle', array(
      'return' => array("contact_id"),
      'id' => $financialAccountId,
    ));
    $contactId = $contactId['contact_id'];

    //check if batch is still open
    if ($paymentProcessorID) {
      $batches = self::getEasyBatches($paymentProcessorID);
    }
    else {
      $batches = self::getEasyBatches(NULL, 'contact_id');
      $batches = array_search($contactId, $batches);
    }

    if (!empty($batches)) {
      return FALSE;
    }
    $title = CRM_Batch_BAO_Batch::generateBatchName();
    if ($paymentProcessorID) {
      $title .= " {$suffixName} " . ts('Auto');
      $batchDate = CRM_Utils_Date::processDate(date("Y-m-d"), date("H:i:s"));
    }
    else {
      $title .= ts(' non-payment transactions auto') . " {$suffixName}";
      $batchDate = CRM_Utils_Date::processDate(date("Y-m-01"), date("H:i:s"));
    }
    $params = array(
      'title' => $title,
      'status_id' => "Open",
      'created_id' => CRM_Core_Session::singleton()->get('userID'),
      'created_date' => CRM_Utils_Date::processDate(date("Y-m-d"), date("H:i:s")),
      'total' => 0.00,
      'item_count' => 0,
    );

    $batch = civicrm_api3('Batch', 'create', $params);
    $entityBatchParams = array(
      'batch_id' => $batch['id'],
      'contact_id' => $contactId,
      'is_automatic' => TRUE,
      'batch_date' => $batchDate,
      'payment_processor_id' => $paymentProcessorID,
    );
    self::create($entityBatchParams);
  }

  /**
   * Create Auto Financial Batch
   */
  public static function createAutoNonPaymentFinancialBatch() {
    $sql = "SELECT id, name, contact_id FROM civicrm_financial_account WHERE is_active = 1 GROUP BY contact_id";
    $dao = CRM_Core_DAO::executeQuery($sql);
    $suffix = NULL;
    while ($dao->fetch()) {
      if ($dao->N > 1) {
        $suffix = CRM_Contact_BAO_Contact::displayName($dao->contact_id);
      }
      self::createAutoFinancialBatch($dao->id, $suffix);
    }
  }

  /**
   * process batches based on daily close time.
   */
  public static function processAutomaticBatches() {
    $openStatusID = CRM_Core_PseudoConstant::getKey('CRM_Batch_BAO_Batch', 'status_id', 'Open');
    $sql = "SELECT e.batch_id, e.payment_processor_id, e.contact_id, p.name
      FROM civicrm_easybatch_entity e
        INNER JOIN civicrm_batch b ON b.id = e.batch_id
        LEFT JOIN civicrm_payment_processor p ON p.id = e.payment_processor_id
      WHERE e.is_automatic = 1
        AND b.status_id = {$openStatusID}
    ";
    $dao = CRM_Core_DAO::executeQuery($sql);
    $closed = array();
    $exportBatch = array();
    $exportFormat = Civi::settings()->get("auto_batch_non_payment_trxns");
    while ($dao->fetch()) {
      $batchStatus = NULL;
      if ($dao->payment_processor_id) {
        $closingTime = Civi::settings()->get("pp_batch_close_time_{$dao->payment_processor_id}");
        $jobRunDate = Civi::settings()->get("pp_last_job_run_{$dao->payment_processor_id}");
        if (!$jobRunDate) {
          $jobRunDate = date('Ymd',strtotime("-1 days"));
        }
        if ($jobRunDate == date('Ymd')) {
          continue;
        }
        if (empty($closingTime)) {
          $closingTime = '11:59:59PM';
        }
        $closingTime = "$jobRunDate $closingTime";
        $closingTime = date('YmdHis', strtotime($closingTime));
        if (date("YmdHis") >= $closingTime) {
          $batchStatus = 'Closed';
        }
      }
      elseif ($exportFormat) {
        $jobRunDate = Civi::settings()->get("npp_last_job_run");
        if (!$jobRunDate) {
          $jobRunDate = date('YmdHis',strtotime("-1 days"));
        }
        if ($jobRunDate > date('Ym01000000')) {
          continue;
        }
        $exportBatch[] = $dao->batch_id;
        $batchStatus = 'Exported';
      }
      if ($batchStatus) {
        $batch = civicrm_api3('Batch', 'create', array(
          'id' => $dao->batch_id,
          'status_id' => $batchStatus,
          'force_close' => TRUE,
          'modified_id' => CRM_Core_Session::singleton()->get('userID'),
        ));
        $closed[] = $dao->batch_id;
        if ($dao->payment_processor_id) {
          $financialAccountId = CRM_Contribute_PseudoConstant::getRelationalFinancialAccount(
            $dao->payment_processor_id,
            NULL,
            'civicrm_payment_processor'
          );
          CRM_EasyBatch_BAO_EasyBatch::createAutoFinancialBatch(
            $financialAccountId,
            $dao->name,
            $dao->payment_processor_id
          );
          Civi::settings()->set("pp_last_job_run_{$dao->payment_processor_id}", date('Ymd'));
        }
      }
    }
    if (!empty($exportBatch)) {
      CRM_Batch_BAO_Batch::exportFinancialBatch($exportBatch, $exportFormat, FALSE);
      CRM_EasyBatch_BAO_EasyBatch::createAutoNonPaymentFinancialBatch();
      Civi::settings()->set("npp_last_job_run", date('YmdHis'));
    }
    return count($closed);
  }

  /**
   * Get All non auto batches.
   */
  public static function getAllNonAutoBatches() {
    $openStatusID = CRM_Core_PseudoConstant::getKey('CRM_Batch_BAO_Batch', 'status_id', 'Open');
    $sql = "SELECT b.id, b.title
      FROM civicrm_batch b
        LEFT JOIN civicrm_easybatch_entity e ON b.id = e.batch_id
      WHERE b.status_id = {$openStatusID} AND (e.is_automatic <> 1 || e.id IS NULL)
    ";
    $dao = CRM_Core_DAO::executeQuery($sql);
    $batches = array();
    while ($dao->fetch()) {
      $batches[$dao->id] = $dao->title;
    }
    return $batches;
  }

  /**
   * Check if auto batch has status open.
   */
  public static function isOpenAutoBatch($batchId) {
    $openStatusID = CRM_Core_PseudoConstant::getKey('CRM_Batch_BAO_Batch', 'status_id', 'Open');
    $sql = "SELECT b.id
      FROM civicrm_batch b
        INNER JOIN civicrm_easybatch_entity e ON e.batch_id = b.id AND b.id = {$batchId}
          AND b.status_id = {$openStatusID} AND e.is_automatic = 1
    ";
    return CRM_Core_DAO::singleValueQuery($sql);
  }

  /**
   * get batch id for last payment.
   */
  public static function getBatchIDForContribution($contributionID) {
    $result = civicrm_api3('EntityFinancialTrxn', 'get', array(
      'return' => array("financial_trxn_id"),
      'entity_table' => "civicrm_contribution",
      'entity_id' => $contributionID,
      'financial_trxn_id.is_payment' => 1,
      'options' => array('limit' => 1, 'sort' => "financial_trxn_id DESC"),
    ));
    $batchId = NULL;
    if ($result['values']) {
      $financialTrxnId = $result['values'][$result['id']]['financial_trxn_id'];
      $result = civicrm_api3('EntityBatch', 'get', array(
        'return' => array("batch_id"),
        'entity_table' => "civicrm_financial_trxn",
        'entity_id' => $financialTrxnId,
      ));
      if ($result['values']) {
        $batchId = $result['values'][$result['id']]['batch_id'];
      }
    }
    return $batchId;
  }

  /**
   * Check if batch edited has transactions assigned to it.
   */
  public static function checkTransactions($batchID, $orgID) {
    $count = civicrm_api3('EntityBatch', 'getCount', array(
      'batch_id' => $batchID,
    ));
    // Transactions present. Check if transactions in batch match the batch company.
    if ($count) {
      $sql = "SELECT fa.contact_id FROM civicrm_entity_batch eb
        INNER JOIN civicrm_financial_trxn ft ON ft.id = eb.entity_id
          AND eb.entity_table = 'civicrm_financial_trxn' AND eb.batch_id = {$batchID}
        INNER JOIN civicrm_financial_account fa ON fa.id = ft.to_financial_account_id
        GROUP BY fa.contact_id";
      $dao = CRM_Core_DAO::executeQuery($sql);
      if ($dao->N > 1) {
        throw new CRM_Core_Exception(ts("All of the transactions in the batch are not associated with the same company."));
      }
      $dao->fetch();
      if ($orgID != $dao->contact_id) {
        $orgName = CRM_Contact_BAO_Contact::displayName($orgID);
        throw new CRM_Core_Exception(ts("All of the transactions in the batch are not associated with the '{$orgName}' company."));
      }
    }
  }

  public static function checkBatchWithSameOrg($batchId, $submitValues) {
    if (empty($submitValues['payment_instrument_id']) && empty($submitValues['payment_processor_id'])) {
      return NULL;
    }
    if (!empty($submitValues['payment_processor_id'])) {
      $paymentMethodOwnerID = self::getPaymentProcessorOwnerID($submitValues['payment_processor_id']);
    }
    else {
      $paymentMethodOwnerID = self::getPaymentMethodOwnerID($submitValues['payment_instrument_id']);
    }
    $ownerID = CRM_Core_DAO::getFieldValue('CRM_EasyBatch_DAO_EasyBatchEntity', $batchId, 'contact_id', 'batch_id');
    if ($paymentMethodOwnerID == $ownerID) {
      return FALSE;
    }
    return TRUE;
  }

  public static function checkFTWithSameOrg($form, $submitValues) {
    $financialTypeID = CRM_Utils_Array::value('financial_type_id', $submitValues);
    $formName = get_class($form);
    $priceSetID = CRM_Utils_Array::value('price_set_id', $submitValues);
    if ($priceSetID) {
      $priceSet = current(CRM_Price_BAO_PriceSet::getSetDetail($priceSetID));
    }
    elseif (!empty($form->_priceSetId)) {
      $priceSetID = $form->_priceSetId;
      $priceSet = $form->_priceSet;
    }
    if (!empty($priceSet)) {
      if (!$financialTypeID) {
        $financialTypeID = $priceSet['financial_type_id'];
      }
      $financialTypes[] = $financialTypeID;
      $lineItem = array();
      if (in_array($formName, array('CRM_Contribute_Form_ContributionPage_Amount', 'CRM_Event_Form_ManageEvent_Fee'))) {
        foreach ($priceSet['fields'] as $pfid => $pf) {
          foreach ($pf['options'] as $pfv) {
            $financialTypes[] = $pfv['financial_type_id'];
          }
        }
      }
      elseif (empty($form->_lineItems)) {
	       CRM_Price_BAO_PriceSet::processAmount($priceSet['fields'],
         $submitValues, $lineItem[$priceSetID]);
         foreach ($lineItem as $lines) {
           foreach ($lines as $item) {
             if (!in_array($item['financial_type_id'], $financialTypes)) {
               $financialTypes[] = $item['financial_type_id'];
             }
           }
         }
      }
      if (count($financialTypes) > 1) {
        self::checkRevenueAndDeferredOwner($financialTypes);
      }
    }
    $financialTypeOwnerID = self::getFinancialTypeOwnerID($financialTypeID);
    if (empty($submitValues['payment_instrument_id']) && empty($submitValues['payment_processor_id']) && empty($submitValues['payment_processor'])) {
      return NULL;
    }

    foreach ((array) $submitValues['payment_processor_id'] as $processorID) {
      $paymentMethodOwnerID = self::getPaymentProcessorOwnerID($processorID);
      if ($financialTypeOwnerID != $paymentMethodOwnerID) {
        throw new CRM_Core_Exception(ts("The Payment Method/Payment Processor and Financial Type should be associated with the same organization."));
      }
    }

    if (!empty($submitValues['payment_instrument_id'])) {
      $paymentMethodOwnerID = self::getPaymentMethodOwnerID($submitValues['payment_instrument_id']);
      if ($financialTypeOwnerID != $paymentMethodOwnerID) {
        throw new CRM_Core_Exception(ts("The Payment Method/Payment Processor and Financial Type should be associated with the same organization."));
      }
    }
  }

  public static function getPaymentMethodOwnerID($paymentInstrumentId) {
    if (empty(self::$_paymentMethodOwnerID[$paymentInstrumentId])) {
      $financialAccountId = CRM_Financial_BAO_FinancialTypeAccount::getInstrumentFinancialAccount($paymentInstrumentId);
      $result = civicrm_api3('FinancialAccount', 'getsingle', array(
        'return' => array("contact_id"),
        'id' => $financialAccountId,
      ));
      self::$_paymentMethodOwnerID[$paymentInstrumentId] = $result['contact_id'];
    }
    return self::$_paymentMethodOwnerID[$paymentInstrumentId];
  }

  public static function getPaymentProcessorOwnerID($processorId) {
    if (empty(self::$_paymentProcessorOwnerID[$processorId])) {
      $financialAccountId = CRM_Contribute_PseudoConstant::getRelationalFinancialAccount($processorId, NULL, 'civicrm_payment_processor');
      $result = civicrm_api3('FinancialAccount', 'getsingle', array(
        'return' => array("contact_id"),
        'id' => $financialAccountId,
      ));
      self::$_paymentProcessorOwnerID[$processorId] = $result['contact_id'];
    }
    return self::$_paymentProcessorOwnerID[$processorId];
  }

  public static function getFinancialTypeOwnerID($financialTypeID) {
    if (!$financialTypeID) {
      return NULL;
    }
    $sql = "SELECT cfa.contact_id FROM civicrm_entity_financial_account cefa
      INNER JOIN civicrm_financial_account cfa ON cefa.financial_account_id = cfa.id
        AND cefa.entity_table = 'civicrm_financial_type' AND cefa.entity_id = {$financialTypeID}
      GROUP BY cfa.contact_id
    ";
    $dao = CRM_Core_DAO::executeQuery($sql);
    if ($dao->N > 1) {
      throw new CRM_Core_Exception(ts("The selected Financial Type's Financial Account should be associated with the same organization."));
    }
    $dao->fetch();
    return $dao->contact_id;
  }

  public static function checkRevenueAndDeferredOwner($financialTypes) {
    $revenue = CRM_Core_PseudoConstant::getKey('CRM_Financial_BAO_FinancialTypeAccount', 'account_relationship', 'Income Account is');
    $deferred = CRM_Core_PseudoConstant::getKey('CRM_Financial_BAO_FinancialTypeAccount', 'account_relationship', 'Deferred Revenue Account is');
    $sql = "SELECT cfa.contact_id FROM civicrm_entity_financial_account cefa
      INNER JOIN civicrm_financial_account cfa ON cefa.financial_account_id = cfa.id
        AND cefa.entity_table = 'civicrm_financial_type'
      WHERE cefa.entity_id IN (" . implode(', ', $financialTypes) . ") AND cefa.account_relationship IN ({$revenue}, {$deferred})
      GROUP BY cfa.contact_id";
    $dao = CRM_Core_DAO::executeQuery($sql);
    if ($dao->N > 1) {
      throw new CRM_Core_Exception(ts("The selected Line item(s) financial type should be associated with the same organization."));
    }
  }

  public static function checkPaymentProcessorOwner($financialAccountId, $paymentIntrumentId) {
    $result = civicrm_api3('FinancialAccount', 'getsingle', array(
      'return' => array("contact_id"),
      'id' => $financialAccountId,
    ));
    $paymentMethodOwnerID = self::getPaymentMethodOwnerID($paymentIntrumentId);
    if ($result['contact_id'] == $paymentMethodOwnerID) {
      return FALSE;
    }
    return TRUE;
  }

}
