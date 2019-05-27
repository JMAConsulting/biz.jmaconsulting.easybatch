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
            'default' => TRUE,
          ),
          'title' => array(
            'title' => ts('Batch Name'),
          ),
          'total' => array(
            'title' => ts('Batch Total'),
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
          'payment_processor_id' => array(
            'title' => ts('Batch payment processor'),
            'dbAlias' => 'pp.name',
          ),
        ),
        'filters' => array(
          'batch_date' => array(
            'title' => ts('Batch Date'),
            'operatorType' => CRM_Report_Form::OP_DATE,
            'type' => CRM_Utils_Type::T_DATE,
          ),
        ),
      ),
      'civicrm_financial_trxn' => array(
        'dao' => 'CRM_Financial_DAO_FinancialTrxn',
        'fields' => array(
          'payment_id' => array(
	          'name' => 'id',
            'title' => ts('Financial Trxn ID/Internal ID'),
            'required' => TRUE,
          ),
          'trxn_date' => array(
            'title' => ts('Transaction Date'),
            'default' => TRUE,
          ),
          'total_amount' => array(
	          'title' => ts('Debit Account Amount (Unsplit)'),
            'default' => TRUE,
	        ),
          'trxn_id' => array(
	          'title' => ts('Transaction ID (Unsplit)'),
	          'default' => TRUE,
          ),
          'payment_instrument_id' => array(
            'title' => ts('Payment Instrument'),
            'required' => TRUE,
            'dbAlias' => 'cov.label',
          ),
          'check_number' => array(
	          'title' => ts('Check Number'),
	          'default' => TRUE,
	        ),
          'currency' => array(
  	        'title' => ts('Currency'),
	          'default' => TRUE,
	        ),
	        'status_id' => array(
	          'title' => ts('Transaction Status'),
	          'default' => TRUE,
	          'dbAlias' => 'cov_status.label',
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
            'default' => TRUE,
          ),
	        'contact_id' => array(
            'name' => 'contact_id',
            'title' => ts('Contact ID'),
            'default' => TRUE,
          ),
	        'contact_name' => array(
            'title' => ts('Contact Name'),
            'dbAlias' => 'cc.sort_name',
          ),
          'receive_date' => array(
            'title' => ts('Receive Date'),
          ),
          'source' => array(
            'title' => ts('Source'),
            'default' => TRUE,
          ),
          'invoice_id' => array(
            'title' => ts('Invoice Number'),
            'default' => TRUE,
            'required' => TRUE,
          ),
        ),
        'filters' => array(),
      ),
      'civicrm_financial_account' => array(
        'dao' => 'CRM_Financial_DAO_FinancialAccount',
        'fields' => array(
          'payment_method_debit_account_name' => array(
            'name' => 'name',
            'title' => ts('Payment Method Financial Debit Account Name'),
            'alias' => 'financial_account_civireport_debit',
          ),
          'payment_method_debit_account_code' => array(
	          'name' => 'accounting_code',
            'title' => ts('Payment Method Account Debit Accounting Code'),
            'alias' => 'financial_account_civireport_debit',
          ),
          'debit_accounting_code' => array(
            'title' => ts('Debit Account'),
            'name' => 'accounting_code',
            'alias' => 'financial_account_civireport_debit',
            'default' => TRUE,
          ),
          'debit_name' => array(
            'title' => ts('Debit Account Name'),
            'name' => 'name',
            'alias' => 'financial_account_civireport_debit',
            'default' => TRUE,
          ),
          'debit_account_type_code' => array(
            'title' => ts('Debit Account Type'),
            'name' => 'account_type_code',
            'alias' => 'financial_account_civireport_debit',
            'default' => TRUE,
          ),
          'credit_accounting_code' => array(
            'title' => ts('Credit Account'),
            'name' => 'accounting_code',
            'alias' => 'financial_account_civireport_credit',
            'default' => TRUE,
            'dbAlias' => "CASE
              WHEN financial_trxn_civireport.from_financial_account_id IS NOT NULL
              THEN  financial_account_civireport_credit_1.credit_accounting_code
              ELSE  financial_account_civireport_credit_2.credit_accounting_code
              END",
          ),
          'credit_name' => array(
            'title' => ts('Credit Account Name'),
            'name' => 'name',
            'alias' => 'financial_account_civireport_credit',
            'default' => TRUE,
            'dbAlias' => "CASE
              WHEN financial_trxn_civireport.from_financial_account_id IS NOT NULL
              THEN  financial_account_civireport_credit_1.credit_name
              ELSE  financial_account_civireport_credit_2.credit_name
              END",
          ),
          'credit_account_type_code' => array(
            'title' => ts('Credit Account Type'),
            'name' => 'account_type_code',
            'alias' => 'financial_account_civireport_credit',
            'default' => TRUE,
            'dbAlias' => "CASE
              WHEN financial_trxn_civireport.from_financial_account_id IS NOT NULL
              THEN  financial_account_civireport_credit_1.account_type_code
              ELSE  financial_account_civireport_credit_2.account_type_code
              END",
          ),
        ),
        'filters' => array(),
      ),
      'civicrm_financial_item' => array(
        'dao' => 'CRM_Financial_DAO_FinancialItem',
        'fields' => array(
          'description' => array(
            'title' => ts('Item Description'),
            'default' => TRUE,
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
          ),
        ),
        'filters' => array(),
      ),
      'civicrm_entity_financial_trxn' => array(
	      'dao' => 'CRM_Financial_DAO_EntityFinancialTrxn',
	      'fields' => array(
	        'debit_amount' => array(
	          'title' => ts('Debit Amount (Split)'),
	          'default' => TRUE,
	          'type' => CRM_Utils_Type::T_STRING,
	        ),
	        'amount' => array(
	          'title' => ts('Amount'),
	          'default' => TRUE,
	          'type' => CRM_Utils_Type::T_STRING,
	        ),
	      ),
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

  public function select() {
    $select = array();

    $this->_columnHeaders = array();
    foreach ($this->_columns as $tableName => $table) {
      if (array_key_exists('fields', $table)) {
        foreach ($table['fields'] as $fieldName => $field) {
          if (!empty($field['required']) ||
            !empty($this->_params['fields'][$fieldName])
          ) {
            switch ($fieldName) {
              case 'credit_accounting_code':
              case 'credit_name':
                $select[] = " CASE
                            WHEN {$this->_aliases['civicrm_financial_trxn']}.from_financial_account_id IS NOT NULL
                            THEN  {$this->_aliases['civicrm_financial_account']}_credit_1.{$field['name']}
                            ELSE  {$this->_aliases['civicrm_financial_account']}_credit_2.{$field['name']}
                            END AS civicrm_financial_account_{$fieldName} ";
                break;

              case 'amount':
              case 'debit_amount':
                $select[] = " CASE
                            WHEN  ceft1.entity_id IS NOT NULL
                            THEN ceft1.amount
                            ELSE ceft.amount
                            END AS civicrm_entity_financial_trxn_{$fieldName} ";
                break;

              case 'credit_contact_id':
                $select[] = " CASE
                            WHEN {$this->_aliases['civicrm_financial_trxn']}.from_financial_account_id IS NOT NULL
                            THEN  credit_contact_1.{$field['name']}
                            ELSE  credit_contact_2.{$field['name']}
                            END AS civicrm_financial_account_{$fieldName} ";
                break;

              default:
                $select[] = "{$field['dbAlias']} as {$tableName}_{$fieldName}";
                break;
            }
            $this->_columnHeaders["{$tableName}_{$fieldName}"]['title'] = $field['title'];
            $this->_columnHeaders["{$tableName}_{$fieldName}"]['type'] = CRM_Utils_Array::value('type', $field);
          }
        }
      }
    }
    // Rearrange select clause
    $order = [
      'civicrm_batch_batch_id',
      'civicrm_contribution_invoice_id',
      'civicrm_contribution_contact_id',
      'civicrm_financial_trxn_payment_id',
      'civicrm_financial_trxn_trxn_date',
      'civicrm_financial_account_debit_accounting_code',
      'civicrm_financial_account_debit_name',
      'civicrm_financial_account_debit_account_type_code',
      'civicrm_financial_trxn_total_amount',
      'civicrm_financial_trxn_trxn_id',
      'civicrm_entity_financial_trxn_debit_amount',
      'civicrm_financial_trxn_payment_instrument_id',
      'civicrm_financial_trxn_check_number',
      'civicrm_contribution_source',
      'civicrm_financial_trxn_currency',
      'civicrm_financial_trxn_status_id',
      'civicrm_entity_financial_trxn_amount',
      'civicrm_financial_account_credit_accounting_code',
      'civicrm_financial_account_credit_name',
      'civicrm_financial_account_credit_account_type_code',
      'civicrm_financial_item_description',
    ];
    $this->_columnHeaders = array_replace(array_flip($order), $this->_columnHeaders);
    unset($this->_columnHeaders['civicrm_contribution_contribution_id']);
    $this->_selectClauses = $select;

    $this->_select = 'SELECT ' . implode(', ', $select) . ' ';
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
        LEFT JOIN civicrm_option_group cog ON cog.name = 'payment_instrument'
        LEFT JOIN civicrm_option_value cov ON (cov.value = {$this->_aliases['civicrm_financial_trxn']}.payment_instrument_id AND cov.option_group_id = cog.id)
        LEFT JOIN civicrm_option_group cog_status ON cog_status.name = 'contribution_status'
        LEFT JOIN civicrm_option_value cov_status ON (cov_status.value = {$this->_aliases['civicrm_financial_trxn']}.status_id AND cov_status.option_group_id = cog_status.id)
        LEFT JOIN civicrm_entity_financial_trxn ceft ON ceft.financial_trxn_id = ceb.entity_id
          AND ceft.entity_table = 'civicrm_contribution'
        LEFT JOIN civicrm_contribution {$this->_aliases['civicrm_contribution']}
          ON {$this->_aliases['civicrm_contribution']}.id = ceft.entity_id
        LEFT JOIN civicrm_contact cc ON cc.id = {$this->_aliases['civicrm_contribution']}.contact_id
        LEFT JOIN civicrm_financial_account {$this->_aliases['civicrm_financial_account']}_debit
          ON {$this->_aliases['civicrm_financial_account']}_debit.id = {$this->_aliases['civicrm_financial_trxn']}.to_financial_account_id
        LEFT JOIN civicrm_financial_account {$this->_aliases['civicrm_financial_account']}_credit_1
          ON {$this->_aliases['civicrm_financial_account']}_credit_1.id = {$this->_aliases['civicrm_financial_trxn']}.from_financial_account_id
        LEFT JOIN civicrm_easybatch_entity {$this->_aliases['civicrm_easybatch_entity']}
          ON {$this->_aliases['civicrm_easybatch_entity']}.batch_id = {$this->_aliases['civicrm_batch']}.id
        LEFT JOIN civicrm_entity_financial_trxn ceft1 ON ceft1.financial_trxn_id = ceb.entity_id
          AND ceft1.entity_table = 'civicrm_financial_item'
        LEFT JOIN civicrm_financial_item {$this->_aliases['civicrm_financial_item']}
          ON {$this->_aliases['civicrm_financial_item']}.id = ceft1.entity_id
        LEFT JOIN civicrm_financial_account {$this->_aliases['civicrm_financial_account']}_credit_2
          ON {$this->_aliases['civicrm_financial_item']}.financial_account_id = {$this->_aliases['civicrm_financial_account']}_credit_2.id
        LEFT JOIN civicrm_financial_account cfa
          ON cfa.id = {$this->_aliases['civicrm_financial_item']}.financial_account_id
        LEFT JOIN civicrm_payment_processor pp
          ON pp.id = {$this->_aliases['civicrm_easybatch_entity']}.payment_processor_id
    ";
  }

  /**
   * Post process function.
   */
  public function postProcess() {
    parent::postProcess();
  }

  public function alterDisplay(&$rows) {
    $prefixValue = Civi::settings()->get('contribution_invoice_settings');
    foreach ($rows as $rowNum => $row) {
      if (array_key_exists('civicrm_contribution_contribution_id', $row)) {
        $rows[$rowNum]['civicrm_contribution_invoice_id'] = CRM_Utils_Array::value('invoice_prefix', $prefixValue) . "" . $row['civicrm_contribution_contribution_id'];
        $entryFound = TRUE;
      }
      if (array_key_exists('civicrm_batch_batch_id', $row)) {
        $value = $row['civicrm_batch_batch_id'];
        $url = CRM_Utils_System::url("civicrm/report/contribute/bookkeeping?", 'force=1&batch_id_value=' . $value);
        $rows[$rowNum]['civicrm_batch_batch_id'] = "<a target='_blank' href=\"$url\">$value</a>";
        $rows[$rowNum]['civicrm_batch_batch_id_hover'] = ts('View Details of Batch transactions.');
      }
      if (!empty($row['civicrm_financial_trxn_card_type_id'])) {
        $rows[$rowNum]['civicrm_financial_trxn_card_type_id'] = $this->getLabels($row['civicrm_financial_trxn_card_type_id'], 'CRM_Financial_DAO_FinancialTrxn', 'card_type_id');
        $entryFound = TRUE;
      }
    }
  }

}
