<?php

namespace Drupal\hello_world\Logger;

use Drupal\Component\Render\FormattableMarkup;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Logger\LogMessageParserInterface;
use Drupal\Core\Logger\RfcLoggerTrait;
use Drupal\Core\Logger\RfcLogLevel;
use Psr\Log\LoggerInterface;

/**
 * A logger that send an email when the log type is "error".
 *
 * @package Drupal\hello_world\Logger
 */
class MailLogger implements LoggerInterface {

  use RfcLoggerTrait;

  /**
   * @var \Drupal\Core\Logger\LogMessageParserInterface
   */
  protected $parser;

  /**
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * MailLogger constructor.
   *
   * @param \Drupal\Core\Logger\LogMessageParserInterface $parser
   * @param \Drupal\Core\Config\ConfigFactoryInterface    $config_factory
   */
  public function __construct(
    LogMessageParserInterface $parser,
    ConfigFactoryInterface $config_factory
  ) {
    $this->parser        = $parser;
    $this->configFactory = $config_factory;
  }

  /**
   * {@inheritDoc}
   */
  public function log($level, $message, array $context = []) {
    if ($level !== RfcLogLevel::ERROR) {
      return;
    }

    $to        = $this->configFactory->get('system.site')->get('mail');
    $langcode  = $this->configFactory->get('system.site')->get('langcode');
    $variables = $this->parser->parseMessagePlaceholders($message, $context);
    $markup    = new FormattableMarkup($message, $variables);
    \Drupal::service('plugin.manager.mail')
           ->mail('hello_world',
             'hello_world_log',
             $to,
             $langcode,
             ['message' => $markup]);
  }

}
