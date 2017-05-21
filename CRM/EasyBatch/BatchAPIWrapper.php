<?php

class CRM_EasyBatch_BatchAPIWrapper implements API_Wrapper {
  /**
   * the wrapper contains a method that allows you to alter the parameters of the api request (including the action and the entity)
   */
  public function fromApiInput($apiRequest) {
    return $apiRequest;
  }

  /**
   * alter the result before returning it to the caller.
   */
  public function toApiOutput($apiRequest, $result) {
    $params = array();
    $postValues = $_REQUEST;
    if (empty($postValues['org_id']) && empty($postValues['batch_date'])) {
      return $result;
    }
    foreach ($apiRequest['params'] as $key => $apiParam) {
      if (in_array($key, array('version', 'options', 'sequential'))) {
        $params[$key] = $apiParam;
        continue;
      }
      elseif ($key == 'return') {
        foreach ($apiParam as $field) {
          $params[$key][] = 'batch_id.' . $field;
        }
      }
      else {
        $params['batch_id.' . $key] = $apiParam;
      }
      
    }
    if (!empty($postValues['batch_date'])) {
      $params['batch_date'] = $postValues['batch_date'];
      $params['return'][] = 'batch_date';
    }
    if (!empty($postValues['org_id'])) {
      $params['contact_id'] = $postValues['org_id'];
      $params['return'][] = 'contact_id';
    }
    $results = civicrm_api3('EasyBatchEntity', 'get', $params);
    $result = $results;
    $result['values'] = array();
    foreach ($results['values'] as $id => $values) {
      foreach ($values as $key => $value) {
        $key = str_replace('batch_id.', '', $key);
        $result['values'][$id][$key] = $value;
      }
    }
    return $result;
  }
}