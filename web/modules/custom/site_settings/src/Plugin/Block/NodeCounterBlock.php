<?php

namespace Drupal\site_settings\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\Core\Entity\EntityTypeManager;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Routing\CurrentRouteMatch;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\site_settings\NodeCounterActionHandler;

/**
 * Provides a 'NodeCounter' Block.
 *
 * @Block(
 *   id = "node_counter",
 *   admin_label = @Translation("Node Counter block"),
 *   category = @Translation("Node Counter"),
 * )
 */
class NodeCounterBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * Constructs a Drupalist object.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\site_settings\NodeCounterActionHandler $nodeCounterService
   *   The Node Counter Service.
   * @param \Drupal\Core\Routing\CurrentRouteMatch $routeMatchService
   *   The Route Match Service
   * @param \Drupal\Core\Entity\EntityTypeManager $entityTypeManager
   *   Entity type manager
   */
  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    protected NodeCounterActionHandler $nodeCounterService,
    protected CurrentRouteMatch $routeMatchService,
    protected EntityTypeManager $entityTypeManager,
  ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('site_settings.node_counter_action_handler'),
      $container->get('current_route_match'),
      $container->get('entity_type.manager'),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $data = NULL;
    $node = $this->routeMatchService->getParameter('node');
    if ($node instanceof \Drupal\node\NodeInterface) {
      $nid = $node->id();
      $nodeCounterData = $this->nodeCounterService->getNodeCounterData($nid);
      $userName = "";
      if ($nodeCounterData->uid > 0) {
        $user = $this->entityTypeManager->getStorage('user')->load($nodeCounterData->uid);
        $userName = $user->name->value;
      }
      $lastViewedDate = (new DrupalDateTime)->format('d.m.y h:i', $nodeCounterData->timestamp);
      $nodeCounterData = [
        'user_name' => $userName,
        'last_viewed_date' => $lastViewedDate,
        'total_count' => $nodeCounterData->totalcount,
        'day_count' => $nodeCounterData->daycount,
      ];
      $data = [
        'node' => $node,
        'node_counter' => $nodeCounterData,
      ];
    }

    return [
      '#theme' => 'node_counter_block',
      '#data' => $data,
      '#attached' => [
        'library' => [
          'site_settings/site_settings',
        ],
      ],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheMaxAge() {
    return 0;
  }

}
