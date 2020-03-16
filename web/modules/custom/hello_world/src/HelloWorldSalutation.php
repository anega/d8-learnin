<?php

namespace Drupal\hello_world;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Prepares the salutation to the world.
 *
 * @package Drupal\hello_world
 */
class HelloWorldSalutation {

  use StringTranslationTrait;

  /**
   * @var \Drupal\Core\Config\ConfigFactoryInterface.
   */
  protected $configFactory;

  /**
   * @var \Symfony\Component\EventDispatcher\EventDispatcherInterface
   */
  protected $eventDispatcher;

  /**
   * HelloWorldSalutation constructor.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface                  $config_factory
   * @param \Symfony\Component\EventDispatcher\EventDispatcherInterface $eventDispatcher
   */
  public function __construct(ConfigFactoryInterface $config_factory, EventDispatcherInterface $eventDispatcher) {
    $this->configFactory = $config_factory;
    $this->eventDispatcher = $eventDispatcher;
  }


  /**
   * Returns the salutation
   */
  public function getSalutation() {
    $config     = $this->configFactory->get('hello_world.custom_salutation');
    $salutation = $config->get('salutation');
    if (!empty($salutation) || !is_null($salutation)) {
      $event = new SalutationEvent();
      $event->setValue($salutation);
      $event = $this->eventDispatcher->dispatch(SalutationEvent::EVENT, $event);

      return $event->getValue();
    }

    $time = new \DateTime();

    if ((int) $time->format('G') >= 06 && (int) $time->format('G') < 12) {
      return $this->t('Good morning world');
    }

    if ((int) $time->format('G') >= 12 && (int) $time->format('G') < 18) {
      return $this->t('Good afternoon world');
    }

    if ((int) $time->format('G') >= 18) {
      return $this->t('Good evening world');
    }
  }

  /**
   * Returns the Salutation render array.
   */
  public function getSalutationComponent() {
    $render = [
      '#theme' => 'hello_world_salutation',
      '#salutation' => [
        '#contextual_links' => [
          'hello_world' => [
            'route_parameters' => []
          ],
        ]
      ]
    ];

    $config = $this->configFactory->get('hello_world.custom_salutation');
    $salutation = $config->get('salutation');

    if ($salutation !== '') {
      $render['#salutation']['#markup'] = $salutation;
      $render['#overridden'] = TRUE;

      return $render;
    }

    $time = new \DateTime();
    $render['#target'] = $this->t('world');

    if ((int) $time->format('G') >= 06 && (int) $time->format('G') < 12) {
      $render['#salutation']['#markup'] = $this->t('Good morning');
      return $render;
    }

    if ((int) $time->format('G') >= 12 && (int) $time->format('G') < 18) {
      $render['#salutation']['#markup'] = $this->t('Good afternoon');
      return $render;
    }

    if ((int) $time->format('G') >= 18) {
      $render['#salutation']['#markup'] = $this->t('Good evening');
      return $render;
    }
  }

}
