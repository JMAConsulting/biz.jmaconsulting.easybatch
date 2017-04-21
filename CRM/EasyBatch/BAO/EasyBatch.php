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
class CRM_EasyBatch_BAO_EasyBatch extends CRM_EasyBatch_DAO_EasyBatch {

  public function __construct() {
    parent::__construct();
  }

  public static function create($params) {
    $entity = new CRM_EasyBatch_DAO_EasyBatch();
    $entity->copyValues($params);
    $entity->save();
    return $entity;
  }

  /**
   * Create entry in easybatch entity table.
   */
  public static function getEasyBatches() {
    $status = CRM_Core_OptionGroup::getValue('batch_status', 'Open', 'name');
    $easyBatches = array();
    $sql = "SELECT e.batch_id, e.contact_id, b.title
      FROM civicrm_easybatch_entity e
      INNER JOIN civicrm_batch b ON b.id = e.batch_id
      WHERE b.status_id = {$status}";
    $dao = CRM_Core_DAO::executeQuery($sql);
    while ($dao->fetch()) {
      $easyBatches[$dao->contact_id][$dao->batch_id] = $dao->title;
    }
    return $easyBatches;
  }

  /**
   * Retrieve all Financial Accounts which have Accounts Receivable relationship.
   *
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
  public static function createEntityEasyBatch($batchId, $cid) {
    $params = array(
      'batch_id' => $batchId,
      'contact_id' => $cid,
    );
    $entity = self::create($params);
    return $entity;
  }

  /**
   * Create Financial Batches for each AR financial account.
   */
  public static function createFinancialBatchForAR() {
    $params = array();
    $financialAccounts = self::getARFinancialAccounts();
    if (!empty($financialAccounts)) {
      foreach ($financialAccounts as $id => $value) {
        $params['title'] = CRM_Batch_BAO_Batch::generateBatchName() . ' ' . $value['name'];
        $params['status_id'] = CRM_Core_OptionGroup::getValue('batch_status', 'Open', 'name');
        $params['created_id'] = CRM_Core_Session::singleton()->get('userID');
        $params['created_date'] = CRM_Utils_Date::processDate(date("Y-m-d"), date("H:i:s"));

        $batch = civicrm_api3('Batch', 'create', $params);
        $entity = self::createEntityEasyBatch($batch['id'], $value['owner']);
      }
    }
  }

}