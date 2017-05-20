<?php
// This file declares a managed database record of type "Job".
// The record will be automatically inserted, updated, or deleted from the
// database as appropriate. For more details, see "hook_civicrm_managed" at:
// http://wiki.civicrm.org/confluence/display/CRMDOC42/Hook+Reference
return array (
  0 => 
  array (
    'name' => 'Process Automatic Batches',
    'entity' => 'Job',
    'params' => 
    array (
      'version' => 3,
      'name' => 'Process Automatic Batches',
      'description' => 'This scheduled job automatically closes batches at the specified closing time and creates new open batches for the next day.',
      'run_frequency' => 'Daily',
      'api_entity' => 'EasybatchEntity',
      'api_action' => 'processAutomaticBatches',
      'parameters' => '',
    ),
  ),
);