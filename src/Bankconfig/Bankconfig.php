<?php

namespace Drupal\commerce_ginger\Bankconfig;
use Symfony\Component\Yaml\Yaml;

/**
 * Class Bankconfig.
 *
 * This class contain configs for a bank
 *
 * @package Drupal\commerce_ginger\Builder
 */

class Bankconfig
{
  const PLATFORM_NAME = 'Drupal9';

  const PLUGIN_NAME = 'ems-online-drupal9';

  const ENDPOINT = 'https://api.dev.gingerpayments.com';

  const LOGGER_CHANEL = 'ginger_plugin';

  public static function getPluginVersion()
  {
    $pluginInfo = Yaml::parseFile(DRUPAL_ROOT . '/modules/contrib/ginger_drupal_commerce/commerce_ginger.info.yml');
    return $pluginInfo['version'] ?? '1.0.0';
  }

  public static function getPluginName()
  {
    return self::PLUGIN_NAME;
  }

  public static function getPlatformName()
  {
    return self::PLATFORM_NAME;
  }

  public static function getEndpoint()
  {
    return self::ENDPOINT;
  }

  public static function getLoggerChanel()
  {
    return self::LOGGER_CHANEL;
  }

  public static function getAfterPayTermsLink($lang)
  {
    $lang == 'nl' ?
      $link = 'https://www.afterpay.nl/nl/algemeen/betalen-met-afterpay/betalingsvoorwaarden' :
      $link = 'https://www.afterpay.nl/en/about/pay-with-afterpay/payment-conditions';
    return $link;
  }
}
