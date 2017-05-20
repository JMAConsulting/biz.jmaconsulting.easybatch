<?php

/**
 * EasyBatchEntity.EasyBatchEntity API
 *
 * @param array $params
 * @return array API result descriptor
 * @see civicrm_api3_create_success
 * @see civicrm_api3_create_error
 * @throws API_Exception
 */
function civicrm_api3_easy_batch_entity_create($params) {
  return _civicrm_api3_basic_create(_civicrm_api3_get_BAO(__FUNCTION__), $params);
}


function civicrm_api3_easy_batch_entity_get($params) {
  return _civicrm_api3_basic_get(_civicrm_api3_get_BAO(__FUNCTION__), $params);
}

/**
 * Close/reopen batches
 *
 * @param array $params
 * @return array API result descriptor
 * @see civicrm_api3_create_success
 * @see civicrm_api3_create_error
 * @throws API_Exception
 */
function civicrm_api3_easy_batch_entity_processAutomaticBatches() {
  $result = CRM_EasyBatch_BAO_EasyBatch::processAutomaticBatches();
  if ($result) {
    return civicrm_api3_create_success(ts('Batches Closed: ' . $result));
  }
  else {
    return civicrm_api3_create_success(ts('No Batches closed.'));
  }
}
