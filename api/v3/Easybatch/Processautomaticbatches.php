<?php

/**
 * Close/reopen batches
 *
 * @param array $params
 * @return array API result descriptor
 * @see civicrm_api3_create_success
 * @see civicrm_api3_create_error
 * @throws API_Exception
 */
function civicrm_api3_easybatch_processautomaticbatches() {
  $result = CRM_EasyBatch_BAO_EasyBatch::processAutomaticBatches();
  if ($result) {
    return civicrm_api3_create_success(ts('Batches Closed: ' . $result));
  }
  else {
    return civicrm_api3_create_success(ts('No Batches closed.'));
  }
}
