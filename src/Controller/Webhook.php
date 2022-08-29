<?php

namespace Drupal\commerce_ginger\Controller;

use Drupal\commerce\Response\NeedsRedirectException;
use Drupal\commerce_ginger\Redefiners\BuildersRedefiner;
use Drupal\commerce_ginger\Builders\OrderBuilder;
use Drupal\commerce_ginger\Builders\ClientBuilder;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\commerce_ginger\Bankconfigs\Bankconfig;
use GingerPluginSdk\Client;

class Webhook
{
  use StringTranslationTrait;

  /**
   * @var OrderBuilder
   */
  private $orderBuilder;
  /**
   * @var Bankconfig
   */
  private $bankconfig;
  /**
   * @var ClientBuilder
   */
  private $clientBuilder;
  /**
   * @var Client
   */
  private $client;

  public function __construct()
  {
    $this->orderBuilder = new OrderBuilder();
    $this->clientBuilder = new ClientBuilder();
    $this->bankconfig = new Bankconfig();
    $this->client = $this->clientBuilder->getClient();
  }

  public function processWebhook(array $webhookData, $entityTypeManager)
  {
    $orderId = filter_var($webhookData['order_id'],FILTER_SANITIZE_STRING);
    $projectId = filter_var($webhookData['project_id'],FILTER_SANITIZE_STRING);

    $payment = $this->orderBuilder->getOrderPaymentByTransactionId($orderId, $entityTypeManager);

    $this->processOrderStatus($orderId, $payment);
  }

  public function processOrderStatus($orderId, $payment)
  {
    $apiOrder =  $this->client->getOrder($orderId);
    $status = $apiOrder->getStatus()->get();

    if ($status) {
      $payment->setState($status);
      $payment->save();
    }
    switch ($status) {
      case 'error':
        \Drupal::logger($this->bankconfig->getLoggerChanel())->error(
          'Order #'.$payment->getOrderId().' Message:'.(current($apiOrder->toArray()['transactions'])['customer_message'] ?? '').' Reason:'. (current($apiOrder->toArray()['transactions'])['reason']) ?? '');
        \Drupal::messenger()->addWarning(current($apiOrder->toArray()['transactions'])['customer_message'] ?? $this->t('Something went wrong, please try again later'));
        throw new NeedsRedirectException($this->orderBuilder->getCancelUrl($payment));
      case 'expired':
        \Drupal::messenger()->addWarning($this->t('Your order is expired, please try again later'));
        throw new NeedsRedirectException($this->orderBuilder->getCancelUrl($payment));
      case 'cancelled':
        \Drupal::messenger()->addWarning($this->t('Your order is cancelled, please try again later'));
        throw new NeedsRedirectException($this->orderBuilder->getCancelUrl($payment));
        break;
      case 'completed':
        \Drupal::messenger()->addMessage($this->t('Thanks for order!'));
        return true;
      case 'processing':
        \Drupal::messenger()->addMessage($this->t('Your order is processing. Thanks!'));
        return true;
      case 'new': if (current($apiOrder->toArray()['transactions'])['payment_method'] == 'bank-transfer') {
        break;
      }

      default:
        throw new NeedsRedirectException($this->orderBuilder->getCancelUrl($payment));
    }

  }
}
