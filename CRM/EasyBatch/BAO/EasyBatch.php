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
        if (empty($closingTime)) {
          $closingTime = '11:59:59PM';
        }
        $closingTime = date('His', strtotime($closingTime));
        if (date("His") >= $closingTime) {
          $batchStatus = 'Closed';
        }
      }
      elseif ($exportFormat) {
        // TODO: add condition to export non payment batch
        $exportBatch[] = $dao->batch_id;
      }

      if ($closeBatch) {
        $batch = civicrm_api3('Batch', 'create', array(
          'id' => $dao->batch_id,
          'status_id' => $batchStatus,
        ));
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
        }
      }
    }
    if (!empty($exportBatch)) {
      CRM_Batch_BAO_Batch::exportFinancialBatch($exportBatch, $exportFormat);
      CRM_EasyBatch_BAO_EasyBatch::createAutoNonPaymentFinancialBatch();
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
}