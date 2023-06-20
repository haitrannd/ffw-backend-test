<?php

namespace Drupal\random_quote\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\random_quote\RandomQuoteService;

/**
 * Provides a 'RandomQuote' Block.
 *
 * @Block(
 *   id = "random_quote",
 *   admin_label = @Translation("Random Quote block"),
 *   category = @Translation("Random Quote"),
 * )
 */
class RandomQuoteBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * Constructs a Drupalist object.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\random_quote\RandomQuoteService $randomQuoteService
   *   The Random Quote Service.
   */
  public function __construct(
    array $configuration, 
    $plugin_id, 
    $plugin_definition, 
    protected RandomQuoteService $randomQuoteService,
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
      $container->get('service.random_quote'),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    return [
      '#theme' => 'random_quote_block',
      '#data' => $this->randomQuoteService->get_quote(),
      '#attached' => [
        'library' => [
          'random_quote/random_quote',
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