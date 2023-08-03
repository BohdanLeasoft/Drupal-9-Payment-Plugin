<?php

namespace Drupal\commerce_ginger\Controller;

use Drupal\commerce\Response\NeedsRedirectException;
use Drupal\commerce_ginger\Redefiner\BuilderRedefiner;
use Drupal\commerce_ginger\Helper\OrderHelper;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\commerce_ginger\Bankconfig\Bankconfig;
use Drupal\commerce_ginger\Controller\OrderController;
use GingerPluginSdk\Client;

class Webhook
{
  use StringTranslationTrait;

  /**
   * @var BuilderRedefiner
   */
  private $builderRedefiner;
  /**
   * @var Client
   */
  private $client;

  public function __construct()
  {
    $this->builderRedefiner = new BuilderRedefiner();
    $this->client = $this->builderRedefiner->getClient();
  }

  public function processWebhook(array $webhookData, $entityTypeManager)
  {
    $orderId = filter_var($webhookData['order_id'],FILTER_SANITIZE_STRING);
    $payment = OrderController::getOrderPaymentByTransactionId($orderId, $entityTypeManager);
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
        \Drupal::logger(Bankconfig::getLoggerChanel())->error(
          'Order #'.$payment->getOrderId().' Message:'.(current($apiOrder->toArray()['transactions'])['customer_message'] ?? '').' Reason:'. (current($apiOrder->toArray()['transactions'])['reason']) ?? '');
        \Drupal::messenger()->addWarning(current($apiOrder->toArray()['transactions'])['customer_message'] ?? $this->t('Something went wrong, please try again later'));
        throw new NeedsRedirectException(OrderHelper::getCancelUrl($payment));
      case 'expired': case 'cancelled':
        \Drupal::messenger()->addWarning($this->t(sprintf('Your order is %s, please try again later', $status)));
        throw new NeedsRedirectException(OrderHelper::getCancelUrl($payment));
        break;
      case 'completed':
        \Drupal::messenger()->addMessage($this->t('Thanks for order!'));
        return;
      case 'processing':
        \Drupal::messenger()->addMessage($this->t('Your order is processing. Thanks!'));
        return;
      case 'new': if ($apiOrder->getCurrentTransaction()->getPaymentMethod()->get() == 'bank-transfer') {
        break;
      }

      default:
        throw new NeedsRedirectException(OrderHelper::getCancelUrl($payment));
    }

  }
}
