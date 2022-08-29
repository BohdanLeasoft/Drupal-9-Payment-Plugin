<?php

namespace Drupal\commerce_ginger\Builders;

use Drupal;
use GingerPluginSdk\Entities\Client as EntitiesClient;
use GingerPluginSdk\Client;
use GingerPluginSdk\Properties\ClientOptions;
use Drupal\commerce_ginger\Bankconfigs\Bankconfig;
use Drupal\Core\StringTranslation\StringTranslationTrait;

/**
 * Class ClientBuilder.
 *
 * This class contain methods for getting ExtraLines and EntitiesClient
 *
 * @package Drupal\commerce_ginger\Builders
 */

class ClientBuilder
{
  use StringTranslationTrait;

  /**
   * @var Bankconfig
   */
  public $bankconfig;
  /**
   * @var string
   */
  private $apiKey;
  /**
   * @var Client
   */
  private $client;


  public function __construct()
  {
    $this->bankconfig = New Bankconfig();

    $settings = Drupal::config('commerce_ginger.settings');
    if (!$settings->get('api_key')) {
      \Drupal::messenger()->addWarning($this->t('Api-Key is missing. Set Api-key in plugin configuration'));
    } else {
      $this->apiKey = $settings->get('api_key');
      $this->client = $this->createClient();
    }
  }

  public function createClient()
  {
    return new Client(
      new ClientOptions(
        endpoint: $this->bankconfig->getEndpoint(),
        useBundle: true,
        apiKey: $this->apiKey)
    );
  }

  /**
   * Collect data for extra_lines
   *
   * @return array
   */
  public function getExtraLines()
  {
    return [
      'user_agent' => $this->getUserAgent(),
      'platform_name' => $this->bankconfig->getPlatformName(),
      'plugin_name' => $this->bankconfig->getPluginName(),
      'plugin_version' => $this->bankconfig->getPluginVersion()
    ];
  }

  /**
   * Return api-key
   *
   * @return string
   */
  public function getApiKey()
  {
    return $this->apiKey;
  }

  /**
   * Return Client
   *
   * @return Client
   */
  public function getClient()
  {
    return $this->client;
  }

    /**
     * Customer user agent for API
     *
     * @return mixed
     */
  public function getUserAgent()
  {
    return $_SERVER['HTTP_USER_AGENT'] ?? null;
  }

  public function getEntitiesClient()
  {
    return new EntitiesClient(
      $this->getUserAgent(),
      $this->bankconfig->getPlatformName(),
      null, // For now no ways to gat platform version were found
      $this->bankconfig->getPluginName(),
      $this->bankconfig->getPluginVersion()
    );
  }
}
