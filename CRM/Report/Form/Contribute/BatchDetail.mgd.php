<?php
// This file declares a managed database record of type "ReportTemplate".
// The record will be automatically inserted, updated, or deleted from the
// database as appropriate. For more details, see "hook_civicrm_managed" at:
// http://wiki.civicrm.org/confluence/display/CRMDOC42/Hook+Reference
return array (
  0 => 
  array (
    'name' => 'CRM_Report_Form_Contribute_BatchDetail',
    'entity' => 'ReportTemplate',
    'params' => 
    array (
      'version' => 3,
      'label' => 'Financial Batch Details Report',
      'description' => 'Financial Batch Details Report(biz.jmaconsulting.easybatch)',
      'class_name' => 'CRM_Report_Form_Contribute_BatchDetail',
      'report_url' => 'biz.jmaconsulting.easybatch/batchdetail',
      'component' => 'CiviContribute',
    ),
  ),
);