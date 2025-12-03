<?php
use CRM_EasyBatch_ExtensionUtil as E;

return [
  'name' => 'EasyBatchEntity',
  'table' => 'civicrm_easybatch_entity',
  'class' => 'CRM_EasyBatch_DAO_EasyBatchEntity',
  'getInfo' => fn() => [
    'title' => E::ts('Easy Batch Entity'),
    'title_plural' => E::ts('Easy Batch Entities'),
    'description' => E::ts(''),
    'log' => TRUE,
  ],
  'getFields' => fn() => [
    'id' => [
      'title' => E::ts('ID'),
      'sql_type' => 'int unsigned',
      'input_type' => 'Number',
      'required' => TRUE,
      'primary_key' => TRUE,
      'auto_increment' => TRUE,
    ],
    'batch_id' => [
      'title' => E::ts('Batch ID'),
      'sql_type' => 'int unsigned',
      'input_type' => 'EntityRef',
      'required' => TRUE,
      'description' => E::ts('FK to Batch ID'),
      'entity_reference' => [
        'entity' => 'Batch',
        'key' => 'id',
        'on_delete' => 'CASCADE',
      ],
    ],
    'contact_id' => [
      'title' => E::ts('Contact ID'),
      'sql_type' => 'int unsigned',
      'input_type' => 'EntityRef',
      'description' => E::ts('FA organization id'),
      'entity_reference' => [
        'entity' => 'Contact',
        'key' => 'id',
        'on_delete' => 'CASCADE',
      ],
    ],
    'payment_processor_id' => [
      'title' => E::ts('Payment Processor ID'),
      'sql_type' => 'int unsigned',
      'input_type' => 'EntityRef',
      'description' => E::ts('FK payment processor id'),
      'entity_reference' => [
        'entity' => 'PaymentProcessor',
        'key' => 'id',
        'on_delete' => 'CASCADE',
      ],
    ],
    'is_automatic' => [
      'title' => E::ts('Is Automatic'),
      'sql_type' => 'boolean',
      'input_type' => 'CheckBox',
      'required' => TRUE,
      'default' => FALSE,
    ],
    'batch_date' => [
      'title' => E::ts('Batch Date'),
      'sql_type' => 'datetime',
      'input_type' => 'Select Date',
      'description' => E::ts('Date for the transactions to be included in the batch.'),
    ],
    'card_type_id' => [
      'title' => E::ts('Card Type'),
      'sql_type' => 'int unsigned',
      'input_type' => 'Select',
      'unique_name' => 'easy_batch_entity_card_type_id',
      'pseudoconstant' => [
        'option_group_name' => 'accept_creditcard',
      ],
    ],
  ],
];
