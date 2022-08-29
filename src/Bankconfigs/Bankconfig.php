<?php

namespace Drupal\commerce_ginger\Bankconfigs;
use Symfony\Component\Yaml\Yaml;

/**
 * Class Bankconfig.
 *
 * This class contain configs for a bank
 *
 * @package Drupal\commerce_ginger\Builders
 */

class Bankconfig
{
  private $pluginVersion = '1.0.0';

  private $platformName = 'Drupal9';

  private $pluginName = 'ems-online-drupal9';

  private $endpoint = 'https://api.online.emspay.eu';

  private $loogerChanel = 'ginger_plugin';

  public function __construct()
  {
    //TODO: Change names to Ginger
    $pluginInfo = Yaml::parseFile(DRUPAL_ROOT . '/modules/contrib/ginger_drupal_commerce/commerce_ginger.info.yml');
    $this->pluginVersion = $pluginInfo['version'];
  }

  public function getPluginVersion()
  {
    return $this->pluginVersion;
  }

  public function getPluginName()
  {
    return $this->pluginName;
  }

  public function getPlatformName()
  {
    return $this->platformName;
  }

  public function getEndpoint()
  {
    return $this->endpoint;
  }

  public function getLoggerChanel()
  {
    return $this->loogerChanel;
  }

  public function getAfterPayTermsLink($lang)
  {
    switch ($lang)
    {
      case 'nl': return 'https://www.afterpay.nl/nl/algemeen/betalen-met-afterpay/betalingsvoorwaarden';
      default: return 'https://www.afterpay.nl/en/about/pay-with-afterpay/payment-conditions';
    }
  }
}
