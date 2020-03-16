<?php

namespace Drupal\hello_world\Plugin\Block;

use Drupal\Core\Annotation\Translation;
use Drupal\Core\Block\Annotation\Block;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\hello_world\HelloWorldSalutation as HelloWorldSalutationService;

/**
 * Hello World Salutation block.
 *
 * @Block(
 *   id="hello_world_salutation_block",
 *   admin_label=@Translation("Hello world salutation"),
 * )
 *
 * @package Drupal\hello_world\Plugin\Block
 */
class HelloWorldSalutationBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * Drupal\hello_world\HelloWorldSalutation definition.
   *
   * @var \Drupal\hello_world\HelloWorldSalutation
   */
  protected $salutation;

  /**
   * HelloWorldSalutationBlock constructor.
   *
   * @param array                                    $configuration
   * @param                                          $plugin_id
   * @param                                          $plugin_definition
   * @param \Drupal\hello_world\HelloWorldSalutation $salutation
   */
  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    HelloWorldSalutationService $salutation
  ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->salutation = $salutation;
  }

  /**
   * @inheritDoc
   */
  public static function create(
    ContainerInterface $container,
    array $configuration,
    $plugin_id,
    $plugin_definition
  ) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('hello_world.salutation')
    );
  }

  /**
   * @inheritDoc
   */
  public function build() {
    return [
      '#markup' => $this->salutation->getSalutation(),
    ];
  }

  /**
   * {@inheritDoc}
   */
  public function defaultConfiguration() {
    return [
      'enabled' => 1,
    ];
  }

  /**
   * {@inheritDoc}
   */
  public function blockForm($form, FormStateInterface $form_state) {
    $config = $this->getConfiguration();

    $form['enabled'] = [
      '#type'          => 'checkbox',
      '#title'         => t('Enabled'),
      '#description'   => t('Check this box if you want to enable this feature.'),
      '#default_value' => $config['enabled'],
    ];

    return $form;
  }

  /**
   * {@inheritDoc}
   *
   * This method is not used, shown only for education purposes.
   */
  public function blockSubmit($form, FormStateInterface $form_state) {
    $this->configuration['enabled'] = $form_state->getValue('enabled');
  }

}
