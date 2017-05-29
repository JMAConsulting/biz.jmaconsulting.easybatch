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
    if (!$result) {
      return $result;
    }
    $params = $easyBatches = array();
    $postValues = $_REQUEST;
    if (empty($postValues['org_id']) && empty($postValues['batch_date'])) {
      if (is_array($result['values'])) {
        foreach ($result['values'] as $id => $values) {
          $resultsBatch = civicrm_api3('EasyBatchEntity', 'get', array('batch_id' => $values['id'], 'return' => array('contact_id.sort_name', 'batch_date')));
          if ($resultsBatch['values']) {
            $easyBatches[$values['id']] = array(
              'org_id' => CRM_Utils_Array::value('contact_id.sort_name', $resultsBatch['values'][$resultsBatch['id']]),
              'batch_date' => CRM_Utils_Array::value('batch_date', $resultsBatch['values'][$resultsBatch['id']]),
            );
          }
        }
        CRM_Core_Smarty::singleton()->assign('easyBatch', $easyBatches);
      }
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
    $params['return'][] = 'contact_id.sort_name';
    $params['return'][] = 'batch_date';
    if (!empty($postValues['batch_date'])) {
      $params['batch_date'] = $postValues['batch_date'];
    }
    if (!empty($postValues['org_id'])) {
      $params['contact_id'] = $postValues['org_id'];
    }
    $results = civicrm_api3('EasyBatchEntity', 'get', $params);
    $result = $results;
    if (is_array($results['values'])) {
      $result['values'] = array();
      foreach ($results['values'] as $id => $values) {
        foreach ($values as $key => $value) {
          $key = str_replace('batch_id.', '', $key);
          $result['values'][$id][$key] = $value;
        }
        $easyBatches[$result['values'][$id]['id']] = array(
          'org_id' => CRM_Utils_Array::value('contact_id.sort_name', $values),
          'batch_date' => CRM_Utils_Array::value('batch_date', $values),
        );
      }
    }
    CRM_Core_Smarty::singleton()->assign('easyBatch', $easyBatches);
    return $result;
  }
}