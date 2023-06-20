<?php

namespace  Drupal\site_settings;

use Drupal\Core\Datetime\DrupalDateTime;

/**
 * providing the service that handle actions (Update/Insert) when user view the node.
 *
 */

class NodeCounterActionHandler {

  /**
   * Checking current node along with the user view the node exist or not in node_counter table.
   *
   * @param int $nid
   *   ID of the node
   * 
   * @return boolean
   *   Return TRUE/FALSE.
   */
  public function node_counter_exist($nid) {
    $data = $this->getNodeCounterData($nid);
    if ($data) {
      return TRUE;
    }
    else {
      return FALSE;
    }
  }

  /**
   * Get counter data of a node.
   *
   * @param int $nid
   *   ID of the node
   * 
   * @return object
   *   Return object contains values from node_counter table.
   */
  public function getNodeCounterData($nid) {
    $data = NULL;
    $result = \Drupal::database()
      ->select('node_counter', 'n')
      ->fields('n', array(
        'nid',
        'uid',
        'timestamp',
        'totalcount',
        'daycount'
      ))
      ->condition('n.nid', $nid)
      ->execute();
    foreach ($result as $record) {
      $data = $record;
    }
    return $data;
  }

  /**
   * Create/Update an item in node_counter table.
   *
   * @param int $nid
   *   ID of the node
   * 
   * @return void
   */
  public function handle_node_counter_action($nid) {
    $currentTimestamp = (new DrupalDateTime())->getTimestamp();
    $currentDate = (new DrupalDateTime())->format('Ymd');
    $uid = \Drupal::currentUser()->id();
    // $nodeCounterExist = $this->node_counter_exist($nid);
    $nodeCounterData = $this->getNodeCounterData($nid);
    $connection = \Drupal::database();
    if(!$nodeCounterData) {
      $fields = array(
        'nid' => $nid,
        'uid' => $uid,
        'totalcount' => 1,
        'daycount' => 1,
        'timestamp' => $currentTimestamp,
      );
      $connection
        ->insert('node_counter')
        ->fields($fields)
        ->execute();
    }
    else {
      $record = $nodeCounterData;
      if (is_object($record)) {
        $totalCount = $record->totalcount + 1;
        $oldDate = (new DrupalDateTime())->format('Ymd', $record->timestamp);
        if ($oldDate == $currentDate) {
          $dayCount = $record->daycount + 1;
        }
        else {
          $dayCount = 1;
        }
        $fields = array(
          'uid' => $uid == 0 ? $record->uid : $uid,
          'totalcount' => $totalCount,
          'daycount' => $dayCount,
          'timestamp' => $currentTimestamp,
        );
        $connection
          ->update('node_counter')
          ->fields($fields)
          ->condition('node_counter.nid', $nid)
          ->execute();
      }
    }
  }

}
