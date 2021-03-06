<?php
/*
+--------------------------------------------------------------------+
| CiviCRM version 4.7                                                |
+--------------------------------------------------------------------+
| Copyright CiviCRM LLC (c) 2004-2017                                |
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
 * @package CRM
 * @copyright CiviCRM LLC (c) 2004-2017
 *
 * Generated from xml/schema/CRM/EasyBatch/EasyBatchEntity.xml
 * DO NOT EDIT.  Generated by CRM_Core_CodeGen
 * (GenCodeChecksum:2216c4a2bd46069a76f9ddc4747d2e4d)
 */
require_once 'CRM/Core/DAO.php';
require_once 'CRM/Utils/Type.php';
/**
 * CRM_EasyBatch_DAO_EasyBatchEntity constructor.
 */
class CRM_EasyBatch_DAO_EasyBatchEntity extends CRM_Core_DAO {
  /**
   * Static instance to hold the table name.
   *
   * @var string
   */
  static $_tableName = 'civicrm_easybatch_entity';
  /**
   * Should CiviCRM log any modifications to this table in the civicrm_log table.
   *
   * @var boolean
   */
  static $_log = true;
  /**
   *
   * @var int unsigned
   */
  public $id;
  /**
   * FK to Batch ID
   *
   * @var int unsigned
   */
  public $batch_id;
  /**
   * FA organization id
   *
   * @var int unsigned
   */
  public $contact_id;
  /**
   * FK payment processor id
   *
   * @var int unsigned
   */
  public $payment_processor_id;
  /**
   *
   * @var boolean
   */
  public $is_automatic;
  /**
   * When was this item created
   *
   * @var datetime
   */
  public $batch_date;
  /**
   *
   * @var int unsigned
   */
  public $card_type_id;
  /**
   * Class constructor.
   */
  function __construct() {
    $this->__table = 'civicrm_easybatch_entity';
    parent::__construct();
  }
  /**
   * Returns foreign keys and entity references.
   *
   * @return array
   *   [CRM_Core_Reference_Interface]
   */
  static function getReferenceColumns() {
    if (!isset(Civi::$statics[__CLASS__]['links'])) {
      Civi::$statics[__CLASS__]['links'] = static ::createReferenceColumns(__CLASS__);
      Civi::$statics[__CLASS__]['links'][] = new CRM_Core_Reference_Basic(self::getTableName() , 'batch_id', 'civicrm_batch', 'id');
      Civi::$statics[__CLASS__]['links'][] = new CRM_Core_Reference_Basic(self::getTableName() , 'contact_id', 'civicrm_contact', 'id');
      Civi::$statics[__CLASS__]['links'][] = new CRM_Core_Reference_Basic(self::getTableName() , 'payment_processor_id', 'civicrm_payment_processor', 'id');
      CRM_Core_DAO_AllCoreTables::invoke(__CLASS__, 'links_callback', Civi::$statics[__CLASS__]['links']);
    }
    return Civi::$statics[__CLASS__]['links'];
  }
  /**
   * Returns all the column names of this table
   *
   * @return array
   */
  static function &fields() {
    if (!isset(Civi::$statics[__CLASS__]['fields'])) {
      Civi::$statics[__CLASS__]['fields'] = array(
        'id' => array(
          'name' => 'id',
          'type' => CRM_Utils_Type::T_INT,
          'required' => true,
          'table_name' => 'civicrm_easybatch_entity',
          'entity' => 'EasyBatchEntity',
          'bao' => 'CRM_EasyBatch_DAO_EasyBatchEntity',
          'localizable' => 0,
        ) ,
        'batch_id' => array(
          'name' => 'batch_id',
          'type' => CRM_Utils_Type::T_INT,
          'description' => 'FK to Batch ID',
          'required' => true,
          'table_name' => 'civicrm_easybatch_entity',
          'entity' => 'EasyBatchEntity',
          'bao' => 'CRM_EasyBatch_DAO_EasyBatchEntity',
          'localizable' => 0,
          'FKClassName' => 'CRM_Batch_DAO_Batch',
        ) ,
        'contact_id' => array(
          'name' => 'contact_id',
          'type' => CRM_Utils_Type::T_INT,
          'description' => 'FA organization id',
          'table_name' => 'civicrm_easybatch_entity',
          'entity' => 'EasyBatchEntity',
          'bao' => 'CRM_EasyBatch_DAO_EasyBatchEntity',
          'localizable' => 0,
          'FKClassName' => 'CRM_Contact_DAO_Contact',
        ) ,
        'payment_processor_id' => array(
          'name' => 'payment_processor_id',
          'type' => CRM_Utils_Type::T_INT,
          'description' => 'FK payment processor id',
          'table_name' => 'civicrm_easybatch_entity',
          'entity' => 'EasyBatchEntity',
          'bao' => 'CRM_EasyBatch_DAO_EasyBatchEntity',
          'localizable' => 0,
          'FKClassName' => 'CRM_Financial_DAO_PaymentProcessor',
        ) ,
        'is_automatic' => array(
          'name' => 'is_automatic',
          'type' => CRM_Utils_Type::T_BOOLEAN,
          'required' => true,
          'table_name' => 'civicrm_easybatch_entity',
          'entity' => 'EasyBatchEntity',
          'bao' => 'CRM_EasyBatch_DAO_EasyBatchEntity',
          'localizable' => 0,
        ) ,
        'batch_date' => array(
          'name' => 'batch_date',
          'type' => CRM_Utils_Type::T_DATE + CRM_Utils_Type::T_TIME,
          'title' => ts('Batch Date') ,
          'description' => 'Date for the transactions to be included in the batch.',
          'table_name' => 'civicrm_easybatch_entity',
          'entity' => 'EasyBatchEntity',
          'bao' => 'CRM_EasyBatch_DAO_EasyBatchEntity',
          'localizable' => 0,
          'html' => array(
            'type' => 'Select Date',
          ) ,
        ) ,
        'card_type_id' => array(
          'name' => 'card_type_id',
          'type' => CRM_Utils_Type::T_INT,
          'description' => 'card type id',
          'table_name' => 'civicrm_easybatch_entity',
          'entity' => 'EasyBatchEntity',
          'bao' => 'CRM_EasyBatch_DAO_EasyBatchEntity',
          'localizable' => 0,
        ) ,
      );
      CRM_Core_DAO_AllCoreTables::invoke(__CLASS__, 'fields_callback', Civi::$statics[__CLASS__]['fields']);
    }
    return Civi::$statics[__CLASS__]['fields'];
  }
  /**
   * Return a mapping from field-name to the corresponding key (as used in fields()).
   *
   * @return array
   *   Array(string $name => string $uniqueName).
   */
  static function &fieldKeys() {
    if (!isset(Civi::$statics[__CLASS__]['fieldKeys'])) {
      Civi::$statics[__CLASS__]['fieldKeys'] = array_flip(CRM_Utils_Array::collect('name', self::fields()));
    }
    return Civi::$statics[__CLASS__]['fieldKeys'];
  }
  /**
   * Returns the names of this table
   *
   * @return string
   */
  static function getTableName() {
    return self::$_tableName;
  }
  /**
   * Returns if this table needs to be logged
   *
   * @return boolean
   */
  function getLog() {
    return self::$_log;
  }
  /**
   * Returns the list of fields that can be imported
   *
   * @param bool $prefix
   *
   * @return array
   */
  static function &import($prefix = false) {
    $r = CRM_Core_DAO_AllCoreTables::getImports(__CLASS__, 'easybatch_entity', $prefix, array());
    return $r;
  }
  /**
   * Returns the list of fields that can be exported
   *
   * @param bool $prefix
   *
   * @return array
   */
  static function &export($prefix = false) {
    $r = CRM_Core_DAO_AllCoreTables::getExports(__CLASS__, 'easybatch_entity', $prefix, array());
    return $r;
  }
  /**
   * Returns the list of indices
   */
  public static function indices($localize = TRUE) {
    $indices = array();
    return ($localize && !empty($indices)) ? CRM_Core_DAO_AllCoreTables::multilingualize(__CLASS__, $indices) : $indices;
  }
}
