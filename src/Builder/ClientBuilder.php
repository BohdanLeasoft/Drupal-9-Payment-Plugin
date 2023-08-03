<?php

namespace Drupal\commerce_ginger\Builder;

use Drupal;
use GingerPluginSdk\Entities\Client as EntitiesClient;
use GingerPluginSdk\Client;
use GingerPluginSdk\Properties\ClientOptions;
use Drupal\commerce_ginger\Bankconfig\Bankconfig;
use Drupal\Core\StringTranslation\StringTranslationTrait;

/**
 * Class ClientBuilder.
 *
 * This class contain methods for getting ExtraLines and EntitiesClient
 *
 * @package Drupal\commerce_ginger\Builder
 */

class ClientBuilder
{
  use StringTranslationTrait;

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
    $settings = Drupal::config('commerce_ginger.settings');
    if (!$settings->get('api_key')) {
      \Drupal::messenger()->addWarning($this->t('Api-Key is missing. Set Api-key in plugin configuration'));
    } else {
      $this->apiKey = $settings->get('api_key');
      $this->client = $this->createClient();
    }
  }

  public function createClient() : Client
  {
    return new Client(
      new ClientOptions(
        endpoint: Bankconfig::getEndpoint(),
        useBundle: true,
        apiKey: $this->apiKey)
    );
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
}
