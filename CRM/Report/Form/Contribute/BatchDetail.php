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
 * @copyright CiviCRM LLC (c) 2004-2016
 * $Id$
 *
 */
class CRM_Report_Form_Contribute_BatchDetail extends CRM_Report_Form {

  /**
   */
  public function __construct() {
    $batchNames = self::getBatches();
    $this->_columns = array(
      'civicrm_batch' => array(
        'dao' => 'CRM_Batch_DAO_Batch',
        'fields' => array(
          'batch_id' => array(
	          'name' => 'id',
            'title' => ts('Batch ID'),
          ),
          'title' => array(
            'title' => ts('Batch Name'),
          ),
        ),
        'filters' => array(
          'id' => array(
            'title' => ts('Batch Name'),
            'type' => CRM_Utils_Type::T_INT,
            'operatorType' => CRM_Report_Form::OP_MULTISELECT,
            'options' => $batchNames,
          ),
          'payment_instrument_id' => array(
            'title' => ts('Payment Method'),
            'operatorType' => CRM_Report_Form::OP_MULTISELECT,
            'options' => CRM_Contribute_PseudoConstant::paymentInstrument(),
            'type' => CRM_Utils_Type::T_INT,
          ),
	     ),
      ),
      'civicrm_easybatch_entity' => array(
        'dao' => 'CRM_EasyBatch_DAO_EasyBatchEntity',
        'fields' => array(
          'batch_date' => array(
            'title' => ts('Batch Date'),
          ),
        ),
        'filters' => array(
          'batch_date' => array(
            'operatorType' => CRM_Report_Form::OP_DATE
          ),
        ),
      ),
      'civicrm_financial_trxn' => array(
        'dao' => 'CRM_Financial_DAO_FinancialTrxn',
        'fields' => array(
          'payment_id' => array(
	    'name' => 'id',
            'title' => ts('Payment ID'),
            'required' => TRUE,
          ),
          'trxn_date' => array(
            'title' => ts('Payment Date'),
          ),
          'payment_instrument_id' => array(
            'title' => ts('Payment Method'),
            'required' => TRUE,
          ),
          'pan_truncation' => array(
            'title' => ts('Last 4 digits of card'),
          ),
          'card_type_id' => array(
            'title' => ts('Card Type'),
          ),
        ),
        'filters' => array(),
      ),
      'civicrm_contribution' => array(
        'dao' => 'CRM_Contribute_DAO_Contribution',
        'fields' => array(
          'contribution_id' => array(
	    'name' => 'id',
            'title' => ts('Contribution ID'),
            'required' => TRUE,
          ),
	  'contact_name' => array(
            'title' => ts('Contact Name'),
            'dbAlias' => 'cc.sort_name',
          ),
          'receive_date' => array(
            'title' => ts('Receive Date'),
          ),
          'source' => array(
            'title' => ts('Contribution Source'),
          ),
        ),
        'filters' => array(),
      ),
      'civicrm_financial_account' => array(
        'dao' => 'CRM_Financial_DAO_FinancialAccount',
        'fields' => array(
          'payment_method_account_name' => array(
	    'name' => 'name',
            'title' => ts('Payment Method Financial Account Name'),
          ),
          'payment_method_account_code' => array(
	    'name' => 'accounting_code',
            'title' => ts('Payment Method Account Accounting Code'),
          ),
        ),
        'filters' => array(),
      ),
      'civicrm_financial_item' => array(
        'dao' => 'CRM_Financial_DAO_FinancialItem',
        'fields' => array(
          'description' => array(
            'title' => ts('Item Description'),
          ),
          'item_account_name' => array(
            'title' => ts('Item Financial Account Name'),
            'dbAlias' => 'cfa.name',
          ),
          'item_account_code' => array(
            'title' => ts('Item Financial Account Accounting Code'),
            'dbAlias' => 'cfa.accounting_code',
          ),
          'item_amount' => array(
	    'name' => 'amount',
            'title' => ts('Item Amount'),
            'required' => TRUE,
          ),
        ),
        'filters' => array(),
      ),
    );
    parent::__construct();
  }

  public static function getBatches() {
    $query = "SELECT id, title
      FROM civicrm_batch
      ORDER BY title";

    $batches = array();
    $dao = CRM_Core_DAO::executeQuery($query);
    while ($dao->fetch()) {
      $batches[$dao->id] = $dao->title;
    }
    return $batches;
  }

  public function preProcess() {
    parent::preProcess();
  }

  /**
   * Set the FROM clause for the report.
   */
  public function from() {
    $this->_from = "
      FROM civicrm_batch {$this->_aliases['civicrm_batch']}
        INNER JOIN civicrm_entity_batch ceb ON ceb.batch_id = {$this->_aliases['civicrm_batch']}.id
        INNER JOIN civicrm_financial_trxn {$this->_aliases['civicrm_financial_trxn']}
          ON {$this->_aliases['civicrm_financial_trxn']}.id = ceb.entity_id
          AND ceb.entity_table = 'civicrm_financial_trxn'
        LEFT JOIN civicrm_entity_financial_trxn ceft ON ceft.financial_trxn_id = ceb.entity_id
          AND ceft.entity_table = 'civicrm_contribution'
        LEFT JOIN civicrm_contribution {$this->_aliases['civicrm_contribution']}
          ON {$this->_aliases['civicrm_contribution']}.id = ceft.entity_id
        LEFT JOIN civicrm_contact cc ON cc.id = {$this->_aliases['civicrm_contribution']}.contact_id
        LEFT JOIN civicrm_financial_account {$this->_aliases['civicrm_financial_account']}
          ON {$this->_aliases['civicrm_financial_account']}.id = {$this->_aliases['civicrm_financial_trxn']}.to_financial_account_id
        LEFT JOIN civicrm_easybatch_entity {$this->_aliases['civicrm_easybatch_entity']}
          ON {$this->_aliases['civicrm_easybatch_entity']}.batch_id = {$this->_aliases['civicrm_batch']}.id
        LEFT JOIN civicrm_entity_financial_trxn ceft1 ON ceft1.financial_trxn_id = ceb.entity_id
          AND ceft1.entity_table = 'civicrm_financial_item'
        LEFT JOIN civicrm_financial_item {$this->_aliases['civicrm_financial_item']}
          ON {$this->_aliases['civicrm_financial_item']}.id = ceft1.entity_id
        LEFT JOIN civicrm_financial_account cfa
          ON cfa.id = {$this->_aliases['civicrm_financial_item']}.financial_account_id
    ";
  }
  /**
   * Post process function.
   */
  public function postProcess() {
    parent::postProcess();
  }

  public function alterDisplay(&$rows) {
    foreach ($rows as $rowNum => $row) {
      if (!empty($row['civicrm_financial_trxn_card_type_id'])) {
        $rows[$rowNum]['civicrm_financial_trxn_card_type_id'] = $this->getLabels($row['civicrm_financial_trxn_card_type_id'], 'CRM_Financial_DAO_FinancialTrxn', 'card_type_id');
        $entryFound = TRUE;
      }
    }
  }

}
