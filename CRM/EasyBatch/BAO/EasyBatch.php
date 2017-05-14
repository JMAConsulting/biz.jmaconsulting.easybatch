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
    $isPayment = FALSE,
    $isAuto = TRUE,
    $paymentProcessorID = NULL,
    $returnColumn = 'title'
  ) {
    $status = CRM_Core_PseudoConstant::getKey('CRM_Batch_BAO_Batch', 'status_id', 'Open');
    $easyBatches = array();
    $where = array("b.status_id = {$status}");
    $where[] = "e.is_automatic = {$isAuto}";
    if ($paymentProcessorID) {
      $where[] = "e.payment_processor_id = {$paymentProcessorID}";
    }
    else {
      $where[] = "e.payment_processor_id IS " . ($isPayment ? 'NOT NULL' : 'NULL');
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
  public static function checkIfFTAddedToBatch($trxnId) {
    $entityBatch = civicrm_api3('EntityBatch', 'get', array(
      'entity_table' => "civicrm_financial_trxn",
      'entity_id' => $trxnId,
    ));
    if ($entityBatch['count'] > 0) {
      return TRUE;
    }
    return FALSE;
  }

  /**
   * Check if batch is still open while trying to save a new contribution.
   */
  public static function checkIfBatchOpen($batchId) {
    $batch = civicrm_api3('Batch', 'get', array(
      'sequential' => 1,
      'return' => array("status_id"),
      'id' => $batchId,
    ));
    if ($batch['values'][0]['status_id'] == CRM_Core_PseudoConstant::getKey('CRM_Batch_BAO_Batch', 'status_id', 'Open')) {
      return TRUE;
    }
    return FALSE;
  }

  /**
   * Add financial transaction entry to batch.
   */
  public static function addToBatch($batchId, $contributionId) {
    $tx = new CRM_Core_Transaction();
    $trxns = civicrm_api3('EntityFinancialTrxn', 'get', array(
      'sequential' => 1,
      'return' => array("financial_trxn_id"),
      'entity_table' => "civicrm_contribution",
      'entity_id' => $contributionId,
      'limit' => 0,
    ));
    if ($trxns['count'] > 0) {
      foreach ($trxns['values'] as $id => $value) {
        if (self::checkIfFTAddedToBatch($value['financial_trxn_id'])) {
          continue;
        }
        civicrm_api3('EntityBatch', 'create', array(
          'entity_table' => "civicrm_financial_trxn",
          'entity_id' => $value['financial_trxn_id'],
          'batch_id' => $batchId,
        ));
      }
    }
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
   * Retrieve all Financial Accounts which have Accounts Receivable relationship.
   *
   * @return array of Financial Accounts
   */
  public static function getARFinancialAccounts() {
    $financialAccounts = array();
    $result = civicrm_api3('EntityFinancialAccount', 'get', array(
      'sequential' => 1,
      'return' => array("financial_account_id.id", "financial_account_id.name", "financial_account_id.contact_id"),
      'entity_table' => "civicrm_financial_type",
      'account_relationship' => "Accounts Receivable Account is",
      'limit' => 0,
    ));
    if ($result['count'] > 0) {
      foreach ($result['values'] as $key => $value) {
        $financialAccounts[$value['financial_account_id.id']] = array(
          'name' => $value['financial_account_id.name'],
          'owner' => $value['financial_account_id.contact_id'],
        );
      }
    }
    return $financialAccounts;
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
      $batches = self::getEasyBatches(TRUE, TRUE, $paymentProcessorID);
    }
    else {
      $batches = self::getEasyBatches(TRUE, TRUE, NULL, 'contact_id');
      $batches = array_search($contactId, $batches);
    }
    if (!empty($batches)) {
      return FALSE;
    }
    $title = CRM_Batch_BAO_Batch::generateBatchName();
    if ($paymentProcessorID) {
      $title .= " {$suffixName} " . ts('Auto');
    }
    else {
      $title .= ts(' non-payment transactions auto') . " {$suffixName}";
    }
    $params = array(
      'title' => $title,
      'status_id' => "Open",
      'created_id' => CRM_Core_Session::singleton()->get('userID'),
      'created_date' => CRM_Utils_Date::processDate(date("Y-m-d"), date("H:i:s")),
    );

    $batch = civicrm_api3('Batch', 'create', $params);
    $entityBatchParams = array(
      'batch_id' => $batch['id'],
      'contact_id' => $contactId,
      'is_automatic' => TRUE,
      'payment_processor_id' => $paymentProcessorID,
    );
    self::create($entityBatchParams);
  }

  /**
   * Create Financial Batch for new account owner.
   */
  public static function openBatch($financialAccount, $contributionId) {
    $params = array(
      'title' => CRM_Batch_BAO_Batch::generateBatchName() . ' ' . $financialAccount['financial_account_id.name'],
      'status_id' => "Open",
      'created_date' => CRM_Utils_Date::processDate(date("Y-m-d"), date("H:i:s")),
    );

    $batch = civicrm_api3('Batch', 'create', $params);
    $entity = self::createEntityEasyBatch($batch['id'], $financialAccount['financial_account_id.contact_id']);
    CRM_EasyBatch_BAO_EasyBatch::addToBatch($batch['id'], $contributionId);
  }

  /**
   * Close/Reopen batches based on daily close time.
   */
  public static function closeReopenBatches() {
    if (!Civi::settings()->get('auto_financial_batch')) {
      return;
    }
    $setting = Civi::settings()->get('batch_close_time_time');
    if (!empty($setting)) {
      $closingTime = date('His', strtotime($setting));
    }
    else {
      $closingTime = date('His', strtotime('11:59:59AM'));
    }
    $closed = array();
    if (date("His") >= $closingTime) {
      $batches = self::getEasyBatches(TRUE);
      if (!empty($batches)) {
        foreach ($batches as $id) {
          $batch = civicrm_api3('Batch', 'create', array(
            'id' => $id,
            'status_id' => "Closed",
          ));
          $closed[] = $batch['id'];
        }
        CRM_EasyBatch_BAO_EasyBatch::createAutoFinancialBatch();
      }
    }
    return count($closed);
  }

}