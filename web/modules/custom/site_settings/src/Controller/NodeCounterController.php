<?php

namespace Drupal\site_settings\Controller;

// use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Class NodeCounterController for handle node counter actions.
 *
 * @package Drupal\site_settings\src\Controller
 */
class NodeCounterController {

  /**
   * @param int $nid
   *   Node ID.
   * 
   * @return JsonResponse
   */
  public function nodeCounter($nid) {
    $response = $this->nodeCounterAction($nid);
    return new JsonResponse($response);
  }

  /**
   * Handle node counter actions.
   * 
   * @param int $nid
   *   Node ID.
   *
   * @return array
   *   Response array.
   */
  public function nodeCounterAction($nid) {
    $service = \Drupal::service('site_settings.node_counter_action_handler');
    $service->handle_node_counter_action($nid);
    return [
      'message' => 'Successful',
      'status' => 200,
      'error' => FALSE
    ];
  }

}
