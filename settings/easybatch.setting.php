<?php
$settings = array(
  'display_financial_batch' => array(
    'group_name' => 'Contribute Preferences',
    'group' => 'contribute',
    'name' => 'display_financial_batch',
    'type' => 'Boolean',
    'html_type' => 'checkbox',
    'default' => 0,
    'add' => '4.7',
    'title' => 'Display Financial Batch on payments through Backoffice forms?',
    'is_domain' => 1,
    'is_contact' => 0,
    'description' => '',
    'help_text' => '',
    'settings_pages' => ['contribute' => ['weight' => 9]],
  ),
  'require_financial_batch' => array(
    'group_name' => 'Contribute Preferences',
    'group' => 'contribute',
    'name' => 'require_financial_batch',
    'type' => 'Boolean',
    'html_type' => 'checkbox',
    'default' => 0,
    'add' => '4.7',
    'title' => 'Require Financial Batch on payments through Backoffice forms?',
    'is_domain' => 1,
    'is_contact' => 0,
    'description' => '',
    'help_text' => '',
    'settings_pages' => ['contribute' => ['weight' => 10]],
  ),
  'auto_batch_non_payment_trxns' => array(
    'group_name' => 'Contribute Preferences',
    'group' => 'contribute',
    'name' => 'auto_batch_non_payment_trxns',
    'type' => 'Integer',
    'html_type' => 'Select',
    'quick_form_type' => 'Element',
    'default' => 0,
    'options' => array(
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
    'settings_pages' => ['contribute' => ['weight' => 11]],
  ),
);

$payment_processor_ids = CRM_Core_DAO::executeQuery("SELECT id FROM civicrm_payment_processor", [], TRUE, NULL, FALSE, FALSE);
while ($payment_processor_ids->fetch()) {
  $paymentProcessorId = $payment_processor_ids->id;
  $settings["pp_auto_financial_batch_{$paymentProcessorId}"] = [
    'name' => "pp_auto_financial_batch_{$paymentProcessorId}",
    'type' => 'Boolean',
    'html_type' => 'checkbox',
    'default' => 0,
    'add' => '1.0',
    'is_domain' => 1,
    'is_contact' => 0,
    'title' => 'Auto create financial batches for this payment processor',
  ];
  $settings["pp_cc_financial_batch_{$paymentProcessorId}"] = [
    'name' => "pp_cc_financial_batch_{$paymentProcessorId}",
    'title' => 'Create separate batches per card type?',
    'type' => 'Boolean',
    'html_type' => 'checkbox',
    'default' => 0,
    'add' => '1.0',
    'is_domain' => 1,
    'is_contact' => 0,
  ];
  $settings["pp_batch_close_time_{$paymentProcessorId}"] = [
    'name' => "pp_batch_close_time_{$paymentProcessorId}",
    'title' => 'Batch Close time',
    'type' => 'String',
    'html_type' => 'datepicker',
    'default' => 0,
    'add' => '1.0',
    'is_domain' => 1,
    'is_contact' => 0,
  ];
  $settings["pp_last_job_run_{$paymentProcessorId}"] = [
    'name' => "pp_last_job_run_{$paymentProcessorId}",
    'title' => 'Batch Cron job last run time for payment processor',
    'type' => 'String',
    'html_type' => 'datepicker',
    'default' => 0,
    'add' => '1.0',
    'is_domain' => 1,
    'is_contact' => 0,
  ];
}
return $settings;
